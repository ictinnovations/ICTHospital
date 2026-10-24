<?php
/**
 * ICTHospital - front desk money: shift cash close, the day book and final settlement.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\DoctorShare;
use App\Models\InvoicePayment;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\ShiftClose;
use App\Models\User;
use App\Support\PaymentModes;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ---------------------------------------------------------------- cash close */

    /**
     * The open shift: payments this cashier took that are not in a close yet.
     * A supervisor with "Cash Close All Users" can look at, and close, anyone's.
     */
    public function cashClose(Request $request)
    {
        $userId = $this->targetUser($request);
        $open = $this->openPayments($userId);

        $history = ShiftClose::query()
            ->when(! Permissions::allows('cash_close_all_users'), fn ($q) => $q->where('user_id', auth()->id()))
            ->orderByDesc('closed_at')->limit(50)->get();

        return view('app.cash.close', [
            'userId' => $userId,
            'users' => Permissions::allows('cash_close_all_users') ? User::orderBy('firstname')->get(['id', 'firstname', 'lastname', 'login']) : collect(),
            'payments' => $open,
            'totals' => $this->totals($open),
            'history' => $history,
            'userNames' => User::get(['id', 'firstname', 'lastname', 'login'])->mapWithKeys(fn ($u) => [$u->id => trim($u->firstname . ' ' . $u->lastname) ?: $u->login]),
        ]);
    }

    public function storeClose(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|integer',
            'counted_cash' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string|max:500',
        ]);
        $userId = $this->targetUser($request);

        $close = DB::transaction(function () use ($userId, $data) {
            $open = InvoicePayment::where('received_by', $userId)->whereNull('shift_close_id')->lockForUpdate()->get();

            if ($open->isEmpty()) {
                throw ValidationException::withMessages(['counted_cash' => 'There is nothing to close: no payments since the last close.']);
            }

            $t = $this->totals($open);
            $counted = isset($data['counted_cash']) && $data['counted_cash'] !== '' ? round((float) $data['counted_cash'], 2) : null;

            $close = ShiftClose::create([
                'user_id' => $userId,
                'opened_at' => $open->min('paid_at'),
                'closed_at' => now(),
                'payments_count' => $t['count'],
                'total' => $t['total'],
                'cash' => $t['cash'], 'bank_transfer' => $t['bank_transfer'], 'card' => $t['card'], 'cheque' => $t['cheque'], 'other' => $t['other'],
                'counted_cash' => $counted,
                'difference' => $counted === null ? null : round($counted - $t['cash'], 2),
                'comment' => $data['comment'] ?? null,
                'closed_by' => auth()->id(),
            ]);

            InvoicePayment::whereIn('id', $open->pluck('id'))->update(['shift_close_id' => $close->id]);

            return $close;
        });

        return redirect('/cash-close/' . $close->id)->with('success', 'Shift closed.');
    }

    public function showClose($id)
    {
        $close = ShiftClose::findOrFail($id);

        if ((int) $close->user_id !== (int) auth()->id() && ! Permissions::allows('cash_close_all_users')) {
            abort(403);
        }

        $payments = InvoicePayment::with('invoice')->where('shift_close_id', $close->id)->orderBy('paid_at')->get();
        $user = User::find($close->user_id);

        return view('app.cash.slip', [
            'close' => $close,
            'payments' => $payments,
            'cashier' => $user ? (trim($user->firstname . ' ' . $user->lastname) ?: $user->login) : '-',
        ]);
    }

    private function targetUser(Request $request)
    {
        $asked = (int) $request->input('user_id', 0);

        return ($asked && Permissions::allows('cash_close_all_users')) ? $asked : (int) auth()->id();
    }

    private function openPayments($userId)
    {
        return InvoicePayment::with('invoice')->where('received_by', $userId)->whereNull('shift_close_id')->orderBy('paid_at')->get();
    }

    /** Money by mode. Write-offs settle invoices but are not money, so they are counted apart. */
    private function totals($payments)
    {
        $t = ['cash' => 0.0, 'bank_transfer' => 0.0, 'card' => 0.0, 'cheque' => 0.0, 'other' => 0.0, 'write_off' => 0.0, 'count' => 0];

        foreach ($payments as $p) {
            if (PaymentModes::isWriteOff($p->method)) {
                $t['write_off'] += (float) $p->amount;
                continue;
            }
            $t[PaymentModes::bucket($p->method)] += (float) $p->amount;
            $t['count']++;
        }
        foreach ($t as $k => $v) {
            $t[$k] = is_float($v) ? round($v, 2) : $v;
        }
        $t['total'] = round($t['cash'] + $t['bank_transfer'] + $t['card'] + $t['cheque'] + $t['other'], 2);

        return $t;
    }

    /* ---------------------------------------------------------------- day book */

    /**
     * What was billed, by service and department, split private / panel / charity,
     * with each line's doctor share against what the hospital keeps; then what was
     * collected in the same days, by payment mode.
     */
    public function dayBook(Request $request)
    {
        $from = (string) $request->input('from', now()->toDateString());
        $to = (string) $request->input('to', $from);
        $kind = (string) $request->input('kind', '');
        $userId = (string) $request->input('user', '');

        $types = PatientType::all()->keyBy('id');
        $default = PatientType::defaultId();

        $invoices = \App\Support\Branch::scope(Payment::query())->with('items')->where('date', '>=', $from)->where('date', '<=', $to)
            ->when($userId !== '', fn ($q) => $q->where('user', $userId))
            ->orderBy('date')->orderBy('id')->get();

        $services = PaymentCategory::all()->keyBy('id');
        $shares = DoctorShare::whereIn('payment_id', $invoices->pluck('id'))->get()->groupBy('invoice_item_id');

        $lines = [];
        $byDept = [];
        $byKind = [];
        foreach ($invoices as $inv) {
            $type = $types->get($inv->patient_type_id ?: $default);
            $bucket = PatientType::bucket(optional($type)->kind);
            if ($kind !== '' && strtolower($bucket) !== $kind) {
                continue;
            }

            $subtotal = (float) $inv->items->sum('line_total');
            $discount = min((float) $inv->discount, $subtotal);
            foreach ($inv->items as $item) {
                $gross = (float) $item->line_total;
                $lineDiscount = $subtotal > 0 ? round($discount * $gross / $subtotal, 2) : 0;
                $net = round($gross - $lineDiscount, 2);
                $doctorShare = round((float) optional($shares->get($item->id))->sum('amount'), 2);
                $dept = optional($services->get($item->payment_category_id))->department ?: 'Not set';

                $lines[] = [
                    'date' => $inv->date, 'invoice' => $inv->id, 'patient' => $inv->patient_name, 'type' => optional($type)->name ?: 'General',
                    'bucket' => $bucket, 'service' => $item->name, 'department' => $dept,
                    'gross' => $gross, 'discount' => $lineDiscount, 'net' => $net, 'doctor' => $doctorShare, 'hospital' => round($net - $doctorShare, 2),
                ];

                $byDept[$dept][$bucket] = round(($byDept[$dept][$bucket] ?? 0) + $net, 2);
                $kindKey = optional($services->get($item->payment_category_id))->service_kind ?: 'other';
                $byKind[$kindKey] = round(($byKind[$kindKey] ?? 0) + $net, 2);
            }
        }
        ksort($byDept);

        $collections = InvoicePayment::whereDate('paid_at', '>=', $from)->whereDate('paid_at', '<=', $to)
            ->when($userId !== '', fn ($q) => $q->where('received_by', $userId))->get();

        $view = [
            'lines' => $lines,
            'byDept' => $byDept,
            'kind_summary' => $byKind,
            'collections' => $this->totals($collections),
            'filters' => compact('from', 'to', 'kind', 'userId'),
            'users' => User::orderBy('firstname')->get(['id', 'firstname', 'lastname', 'login']),
        ];

        if ($request->input('export') === 'csv') {
            $csv = "Date,Invoice,Patient,Patient type,Group,Service,Department,Gross,Discount,Net,Doctor share,Hospital share\n";
            foreach ($lines as $l) {
                $csv .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"', [
                    $l['date'], $l['invoice'], $l['patient'], $l['type'], $l['bucket'], $l['service'], $l['department'],
                    $l['gross'], $l['discount'], $l['net'], $l['doctor'], $l['hospital'],
                ])) . "\n";
            }

            return response($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="day-book-' . $from . '-to-' . $to . '.csv"']);
        }

        return view('app.cash.daybook', $view);
    }

    /* ---------------------------------------------------------------- final settlement */

    public function settlement(Request $request)
    {
        $patient = $request->input('patient') ? Patient::find($request->input('patient')) : null;
        $invoices = $patient ? Payment::with(['items', 'payments'])->where('patient', $patient->id)->orderBy('date')->orderBy('id')->get() : collect();

        $sum = fn ($f) => round((float) $invoices->sum($f), 2);
        $paid = round((float) $invoices->sum(fn ($i) => $i->payments->reject(fn ($p) => PaymentModes::isWriteOff($p->method))->sum('amount')), 2);
        $writtenOff = round((float) $invoices->sum(fn ($i) => $i->payments->filter(fn ($p) => PaymentModes::isWriteOff($p->method))->sum('amount')), 2);

        return view('app.cash.settlement', [
            'patient' => $patient,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'invoices' => $invoices,
            'base' => $sum('amount'),
            'discount' => $sum('discount'),
            'net' => $sum('gross_total'),
            'paid' => $paid,
            'writtenOff' => $writtenOff,
            'receivable' => round(max(0, $invoices->sum(fn ($i) => $i->balance())), 2),
        ]);
    }

    /**
     * Takes the final payment across all of a patient's open invoices, oldest
     * first, and optionally writes off what is left with a reason. One action at
     * discharge instead of paying each invoice in turn.
     */
    public function settle(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $data = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'method' => 'required|in:' . implode(',', PaymentModes::MODES),
            'reference' => 'nullable|string|max:120',
            'write_off' => 'nullable|numeric|min:0',
            'write_off_reason' => 'nullable|string|max:120',
        ]);

        $amount = round((float) ($data['amount'] ?? 0), 2);
        $writeOff = round((float) ($data['write_off'] ?? 0), 2);
        if ($writeOff > 0 && trim((string) ($data['write_off_reason'] ?? '')) === '') {
            throw ValidationException::withMessages(['write_off_reason' => 'Say why the balance is being written off.']);
        }
        if ($writeOff > 0 && ! Permissions::allows('payment_update')) {
            throw ValidationException::withMessages(['write_off' => 'Your role cannot write off a balance.']);
        }
        if ($amount <= 0 && $writeOff <= 0) {
            throw ValidationException::withMessages(['amount' => 'Enter the amount received or a write-off.']);
        }

        DB::transaction(function () use ($patient, $amount, $writeOff, $data) {
            $invoices = Payment::where('patient', $patient->id)->orderBy('date')->orderBy('id')->lockForUpdate()->get();
            $outstanding = round($invoices->sum(fn ($i) => max(0, $i->balance())), 2);

            if ($amount + $writeOff > $outstanding + 0.004) {
                throw ValidationException::withMessages(['amount' => sprintf('Only %s is outstanding for this patient.', number_format($outstanding, 2))]);
            }

            foreach ([[$amount, $data['method'], $data['reference'] ?? null], [$writeOff, PaymentModes::WRITE_OFF, $data['write_off_reason'] ?? null]] as [$left, $method, $ref]) {
                foreach ($invoices as $inv) {
                    if ($left <= 0.004) {
                        break;
                    }
                    $bal = $inv->balance();
                    if ($bal <= 0.004) {
                        continue;
                    }
                    $take = round(min($bal, $left), 2);
                    $inv->payments()->create([
                        'amount' => number_format($take, 2, '.', ''), 'method' => $method, 'reference' => $ref,
                        'paid_at' => now()->toDateTimeString(), 'received_by' => auth()->id(),
                    ]);
                    $left = round($left - $take, 2);
                    $fresh = $inv->fresh('payments');
                    $inv->update(['amount_received' => number_format($fresh->paid(), 2, '.', ''), 'status' => $fresh->settlement()]);
                }
            }
        });

        return redirect('/settlement?patient=' . $patient->id)->with('success', 'Settlement recorded.');
    }
}
