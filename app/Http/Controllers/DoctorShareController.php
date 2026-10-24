<?php
/**
 * ICTHospital - doctor share policies, the share report, approval and payout.
 *
 * Shares are worked out when an invoice is raised (see ShareCalculator). A share can
 * only be approved once its invoice is paid in full, because a hospital pays the
 * doctor out of money it has collected, and approved shares are paid out in a batch
 * that is also written to the accounting ledger as an expense.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\AccountSector;
use App\Models\Doctor;
use App\Models\DoctorShare;
use App\Models\PatientType;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\SharePolicy;
use App\Models\ShareRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorShareController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ---------------------------------------------------------------- policies */

    public function policies()
    {
        return view('app.doctor-share.policies', [
            'policies' => SharePolicy::with('rules')->orderBy('name')->get(),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name', 'department', 'share_policy_id']),
            'services' => PaymentCategory::orderBy('category')->get(['id', 'category', 'department']),
            'departments' => PaymentCategory::whereNotNull('department')->where('department', '!=', '')->distinct()->orderBy('department')->pluck('department'),
            'types' => PatientType::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storePolicy(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:share_policy,name',
            'notes' => 'nullable|string|max:255',
            'doctor_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $policy = DB::transaction(function () use ($data) {
            $policy = SharePolicy::create(['name' => $data['name'], 'notes' => $data['notes'] ?? null, 'active' => true]);
            // Start every policy with a catch-all rule so it does something straight away.
            ShareRule::create(['share_policy_id' => $policy->id, 'doctor_percent' => $data['doctor_percent'] ?? 0, 'referral_percent' => 0]);

            return $policy;
        });

        return redirect('/doctor-share/policies#policy-' . $policy->id)->with('success', 'Policy ' . $policy->name . ' added.');
    }

    public function updatePolicy(Request $request, $id)
    {
        $policy = SharePolicy::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('share_policy', 'name')->ignore($policy->id)],
            'notes' => 'nullable|string|max:255',
        ]);
        $policy->update(['name' => $data['name'], 'notes' => $data['notes'] ?? null]);

        return redirect('/doctor-share/policies#policy-' . $policy->id)->with('success', 'Saved ' . $policy->name . '.');
    }

    public function destroyPolicy($id)
    {
        $policy = SharePolicy::findOrFail($id);

        if (Doctor::where('share_policy_id', $policy->id)->exists()) {
            return redirect('/doctor-share/policies')->with('error', 'Doctors are still on ' . $policy->name . '. Move them to another policy first.');
        }

        DB::transaction(function () use ($policy) {
            ShareRule::where('share_policy_id', $policy->id)->delete();
            $policy->delete();
        });

        return redirect('/doctor-share/policies')->with('success', 'Policy deleted.');
    }

    public function storeRule(Request $request, $id)
    {
        $policy = SharePolicy::findOrFail($id);
        ShareRule::create(['share_policy_id' => $policy->id] + $this->validatedRule($request));

        return redirect('/doctor-share/policies#policy-' . $policy->id)->with('success', 'Rule added to ' . $policy->name . '.');
    }

    public function updateRule(Request $request, $id)
    {
        $rule = ShareRule::findOrFail($id);
        $rule->update($this->validatedRule($request));

        return redirect('/doctor-share/policies#policy-' . $rule->share_policy_id)->with('success', 'Rule saved. New invoices use it; shares already worked out are not changed.');
    }

    public function destroyRule($id)
    {
        $rule = ShareRule::findOrFail($id);
        $rule->delete();

        return redirect('/doctor-share/policies#policy-' . $rule->share_policy_id)->with('success', 'Rule removed.');
    }

    /** Puts the ticked doctors on this policy. Doctors unticked here are taken off it. */
    public function assign(Request $request, $id)
    {
        $policy = SharePolicy::findOrFail($id);
        $ids = array_map('intval', (array) $request->input('doctors', []));

        DB::transaction(function () use ($policy, $ids) {
            Doctor::where('share_policy_id', $policy->id)->whereNotIn('id', $ids ?: [0])->update(['share_policy_id' => null]);
            if ($ids) {
                Doctor::whereIn('id', $ids)->update(['share_policy_id' => $policy->id]);
            }
        });

        return redirect('/doctor-share/policies#policy-' . $policy->id)->with('success', count($ids) . ' doctor(s) on ' . $policy->name . '.');
    }

    private function validatedRule(Request $request)
    {
        $data = $request->validate([
            'payment_category_id' => 'nullable|integer|exists:payment_category,id',
            'department' => 'nullable|string|max:100',
            'patient_type_id' => 'nullable|integer|exists:patient_type,id',
            'doctor_percent' => 'required|numeric|min:0|max:100',
            'referral_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        return [
            'payment_category_id' => $data['payment_category_id'] ?? null,
            'department' => isset($data['department']) && trim($data['department']) !== '' ? trim($data['department']) : null,
            'patient_type_id' => $data['patient_type_id'] ?? null,
            'doctor_percent' => $data['doctor_percent'],
            'referral_percent' => $data['referral_percent'] ?? 0,
        ];
    }

    /* ---------------------------------------------------------------- report and payout */

    public function report(Request $request)
    {
        $from = (string) $request->input('from', now()->startOfMonth()->toDateString());
        $to = (string) $request->input('to', now()->toDateString());
        $doctor = (string) $request->input('doctor', '');
        $role = (string) $request->input('role', '');
        $status = (string) $request->input('status', '');
        $paid = (string) $request->input('paid', '');

        $shares = DoctorShare::query()
            ->with(['invoice.payments', 'item'])
            ->whereHas('invoice', fn ($q) => $q->where('date', '>=', $from)->where('date', '<=', $to))
            ->when($doctor !== '', fn ($q) => $q->where('doctor_id', $doctor))
            ->when($role !== '', fn ($q) => $q->where('role', $role))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('doctor_id')->orderBy('payment_id')->get();

        if ($paid === 'paid') {
            $shares = $shares->filter(fn ($s) => $s->invoice && $s->invoice->balance() <= 0.004);
        } elseif ($paid === 'unpaid') {
            $shares = $shares->filter(fn ($s) => $s->invoice && $s->invoice->balance() > 0.004);
        }

        $doctors = Doctor::orderBy('name')->get(['id', 'name']);

        return view('app.doctor-share.report', [
            'shares' => $shares,
            'doctorNames' => $doctors->pluck('name', 'id'),
            'doctors' => $doctors,
            'byDoctor' => $shares->groupBy('doctor_id')->map(fn ($g) => [
                'pending' => $g->where('status', 'pending')->sum('amount'),
                'approved' => $g->where('status', 'approved')->sum('amount'),
                'paid' => $g->where('status', 'paid')->sum('amount'),
                'hospital' => $g->where('role', 'performing')->sum(fn ($s) => (float) $s->base_amount - (float) $s->amount),
            ]),
            'filters' => compact('from', 'to', 'doctor', 'role', 'status', 'paid'),
        ]);
    }

    public function approve(Request $request)
    {
        $ids = array_map('intval', (array) $request->input('ids', []));
        $approved = 0;
        $skipped = 0;

        DB::transaction(function () use ($ids, &$approved, &$skipped) {
            foreach (DoctorShare::with('invoice.payments')->whereIn('id', $ids)->where('status', 'pending')->lockForUpdate()->get() as $share) {
                if (! $share->invoice || $share->invoice->balance() > 0.004) {
                    $skipped++;
                    continue;
                }
                $share->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
                $approved++;
            }
        });

        $msg = $approved . ' share(s) approved.';
        if ($skipped) {
            $msg .= ' ' . $skipped . ' skipped because the invoice is not paid in full yet.';
        }

        return back()->with($approved ? 'success' : 'error', $msg);
    }

    /**
     * Pays approved shares. One ledger expense is written per doctor so the
     * accounting reports show doctor payouts without listing every line.
     */
    public function pay(Request $request)
    {
        $data = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer', 'reference' => 'nullable|string|max:60']);
        $ref = $data['reference'] ?? ('PAYOUT-' . now()->format('YmdHi'));
        $count = 0;

        DB::transaction(function () use ($data, $ref, &$count) {
            $shares = DoctorShare::whereIn('id', $data['ids'])->where('status', 'approved')->lockForUpdate()->get();
            if ($shares->isEmpty()) {
                return;
            }

            AccountSector::firstOrCreate(['name' => 'Doctor share', 'type' => 'Expence']);
            $names = Doctor::whereIn('id', $shares->pluck('doctor_id'))->pluck('name', 'id');

            foreach ($shares->groupBy('doctor_id') as $doctorId => $group) {
                Accounting::create([
                    'name' => 'Doctor share', 'type' => 'Expence', 'amount' => round($group->sum('amount'), 2),
                    'date' => now()->toDateString(),
                    'description' => ($names[$doctorId] ?? 'Doctor ' . $doctorId) . ': ' . $group->count() . ' line(s), ' . $ref,
                ]);
            }

            DoctorShare::whereIn('id', $shares->pluck('id'))->update(['status' => 'paid', 'paid_at' => now(), 'payout_ref' => $ref]);
            $count = $shares->count();
        });

        return back()->with($count ? 'success' : 'error', $count ? $count . ' share(s) paid, reference ' . $ref . '.' : 'Only approved shares can be paid.');
    }
}
