<?php
/**
 * ICTHospital - patient types and the price list per type.
 *
 * The services screen keeps the list price. This screen sets what each patient type
 * pays for each service, separately for outpatients and inpatients, and covers the
 * chores a billing office actually does with a price list: copy one company's list
 * to start a new one, put a whole list up or down by a percentage, and download it.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientType;
use App\Models\PaymentCategory;
use App\Models\ServicePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PricingController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ---------------------------------------------------------------- patient types */

    public function types()
    {
        return view('app.pricing.types', [
            'types' => PatientType::orderBy('name')->get(),
            'counts' => Patient::selectRaw('patient_type_id, count(*) as n')->groupBy('patient_type_id')->pluck('n', 'patient_type_id'),
            'kinds' => PatientType::KINDS,
        ]);
    }

    public function storeType(Request $request)
    {
        PatientType::create($this->validatedType($request));

        return redirect('/patient-types')->with('success', 'Patient type added.');
    }

    public function updateType(Request $request, $id)
    {
        $type = PatientType::findOrFail($id);
        $type->update($this->validatedType($request, $type->id));

        return redirect('/patient-types')->with('success', 'Saved ' . $type->name . '.');
    }

    /** A type in use is switched off rather than deleted, so old invoices keep their label. */
    public function destroyType($id)
    {
        $type = PatientType::findOrFail($id);

        if (Patient::where('patient_type_id', $type->id)->exists() || DB::table('payment')->where('patient_type_id', $type->id)->exists()) {
            $type->update(['active' => false]);

            return redirect('/patient-types')->with('success', $type->name . ' is in use, so it was switched off instead of deleted.');
        }

        ServicePrice::where('patient_type_id', $type->id)->delete();
        $type->delete();

        return redirect('/patient-types')->with('success', 'Deleted ' . $type->name . '.');
    }

    private function validatedType(Request $request, $ignore = null)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('patient_type', 'name')->ignore($ignore)],
            'kind' => 'required|in:' . implode(',', array_keys(PatientType::KINDS)),
            'company' => 'nullable|string|max:120',
            'active' => 'nullable|boolean',
        ]);
        $data['company'] = $data['company'] ?? null;
        $data['active'] = (bool) ($data['active'] ?? false);

        return $data;
    }

    /* ---------------------------------------------------------------- price list */

    public function index(Request $request)
    {
        $types = PatientType::orderBy('name')->get();
        $typeId = (int) ($request->input('type') ?: PatientType::defaultId() ?: optional($types->first())->id);
        $term = trim((string) $request->input('q', ''));
        $department = (string) $request->input('department', '');

        $services = PaymentCategory::query()
            ->when($term !== '', fn ($q) => $q->where('category', 'like', '%' . $term . '%'))
            ->when($department !== '', fn ($q) => $q->where('department', $department))
            ->orderBy('category')->get();

        return view('app.pricing.index', [
            'types' => $types,
            'typeId' => $typeId,
            'type' => $types->firstWhere('id', $typeId),
            'services' => $services,
            'prices' => ServicePrice::where('patient_type_id', $typeId)->get()->keyBy('payment_category_id'),
            'term' => $term,
            'department' => $department,
            'departments' => PaymentCategory::whereNotNull('department')->where('department', '!=', '')->distinct()->orderBy('department')->pluck('department'),
        ]);
    }

    /** Saves the whole visible list in one go. A blank box clears that price back to the list price. */
    public function save(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|integer|exists:patient_type,id',
            'opd' => 'array', 'opd.*' => 'nullable|numeric|min:0',
            'ipd' => 'array', 'ipd.*' => 'nullable|numeric|min:0',
        ]);

        $ids = array_unique(array_merge(array_keys($data['opd'] ?? []), array_keys($data['ipd'] ?? [])));

        DB::transaction(function () use ($ids, $data) {
            foreach ($ids as $serviceId) {
                $opd = $data['opd'][$serviceId] ?? null;
                $ipd = $data['ipd'][$serviceId] ?? null;
                $opd = ($opd === null || $opd === '') ? null : round((float) $opd, 2);
                $ipd = ($ipd === null || $ipd === '') ? null : round((float) $ipd, 2);

                if ($opd === null && $ipd === null) {
                    ServicePrice::where('payment_category_id', $serviceId)->where('patient_type_id', $data['type'])->delete();
                    continue;
                }

                ServicePrice::updateOrCreate(
                    ['payment_category_id' => $serviceId, 'patient_type_id' => $data['type']],
                    ['opd_price' => $opd, 'ipd_price' => $ipd]
                );
            }
        });

        return redirect('/pricing?type=' . $data['type'])->with('success', 'Price list saved.');
    }

    /** Copies one type's list onto another, replacing what the target had. */
    public function copy(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|integer|exists:patient_type,id',
            'to' => 'required|integer|different:from|exists:patient_type,id',
        ]);

        $count = DB::transaction(function () use ($data) {
            ServicePrice::where('patient_type_id', $data['to'])->delete();
            $rows = ServicePrice::where('patient_type_id', $data['from'])->get();
            foreach ($rows as $row) {
                ServicePrice::create([
                    'payment_category_id' => $row->payment_category_id, 'patient_type_id' => $data['to'],
                    'opd_price' => $row->opd_price, 'ipd_price' => $row->ipd_price,
                ]);
            }

            return $rows->count();
        });

        return redirect('/pricing?type=' . $data['to'])->with('success', 'Copied ' . $count . ' price(s).');
    }

    /**
     * Puts every price of a type up or down by a percentage.
     *
     * Services with no price for the type start from the list price, so "everything
     * 10% above the general rate" is one action rather than a typing exercise.
     */
    public function adjust(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|integer|exists:patient_type,id',
            'percent' => 'required|numeric|min:-90|max:500',
            'setting' => 'required|in:opd,ipd,both',
        ]);
        $factor = 1 + ((float) $data['percent']) / 100;
        $settings = $data['setting'] === 'both' ? ['opd', 'ipd'] : [$data['setting']];

        DB::transaction(function () use ($data, $factor, $settings) {
            foreach (PaymentCategory::all() as $service) {
                $row = ServicePrice::firstOrNew(['payment_category_id' => $service->id, 'patient_type_id' => $data['type']]);
                foreach ($settings as $s) {
                    $base = $row->{$s . '_price'} !== null ? (float) $row->{$s . '_price'} : (float) $service->c_price;
                    $row->{$s . '_price'} = round($base * $factor, 2);
                }
                $row->save();
            }
        });

        return redirect('/pricing?type=' . $data['type'])->with('success', 'Prices adjusted by ' . $data['percent'] . '%.');
    }

    public function export(Request $request)
    {
        $type = PatientType::findOrFail((int) ($request->input('type') ?: PatientType::defaultId()));
        $prices = ServicePrice::where('patient_type_id', $type->id)->get()->keyBy('payment_category_id');

        $rows = [['Service', 'Department', 'List price', 'OPD price', 'IPD price']];
        foreach (PaymentCategory::orderBy('category')->get() as $s) {
            $p = $prices->get($s->id);
            $rows[] = [$s->category, $s->department, $s->c_price,
                $p && $p->opd_price !== null ? $p->opd_price : $s->c_price,
                $p && $p->ipd_price !== null ? $p->ipd_price : $s->c_price];
        }

        $csv = '';
        foreach ($rows as $r) {
            $csv .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"', $r)) . "\n";
        }

        $file = 'price-list-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($type->name)) . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $file . '"',
        ]);
    }
}
