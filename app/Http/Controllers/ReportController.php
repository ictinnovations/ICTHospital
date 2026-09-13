<?php
/**
 * ICTHospital - reports across the clinical child tables.
 *
 * Replacing the packed varchar columns with real rows was the point of the
 * rebuild: prescription_medicine instead of prescription.medicine, lab_test
 * instead of lab.category_name, pharmacy_sale_item and invoice_item instead of
 * their comma separated equivalents. None of that earns anything until something
 * queries it, and nothing did. These four reports are the questions a hospital
 * asks most often, and each one is only answerable because of those tables.
 *
 * Every figure here is derived at read time. There is no summary table to fall
 * out of step, which is the failure this system inherited everywhere it had a
 * hand set status column.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\AllotedBed;
use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Lab;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PharmacySaleItem;
use App\Models\PrescriptionMedicine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** The menu. Each card is hidden unless the role can open what it points at. */
    public function index()
    {
        return view('app.reports.index');
    }

    /**
     * Lab work that has been requested and not yet reported.
     *
     * Answerable only because a request is rows in lab_test rather than one
     * report blob, so a request with the bloods back and the culture still
     * growing shows the culture and not the whole request.
     */
    public function lab(Request $request)
    {
        $tests = LabTest::query()
            ->where(function ($q) {
                $q->whereNull('result')->orWhere('result', '');
            })
            ->orderBy('lab_id')
            ->get();

        $labs = Lab::whereIn('id', $tests->pluck('lab_id')->unique())->get()->keyBy('id');
        $patients = Patient::whereIn('id', $labs->pluck('patient')->filter()->unique())
            ->get(['id', 'name', 'patient_id'])->keyBy('id');

        $rows = $tests->map(function ($test) use ($labs, $patients) {
            $lab = $labs[$test->lab_id] ?? null;
            $requested = $lab && $lab->date ? Carbon::parse($lab->date) : null;

            return [
                'test' => $test,
                'lab' => $lab,
                'patient' => $lab ? ($patients[$lab->patient] ?? null) : null,
                // Age in whole days, which is how a lab actually talks about a
                // backlog. Anything past three days is the part worth chasing.
                'days' => $requested ? $requested->diffInDays(now()) : null,
            ];
        })->sortByDesc('days')->values();

        return view('app.reports.lab', [
            'rows' => $rows,
            'overdue' => $rows->filter(fn ($r) => ($r['days'] ?? 0) >= 3)->count(),
            'byTest' => $tests->groupBy('name')->map->count()->sortDesc(),
        ]);
    }

    /**
     * What was prescribed against what was actually handed over.
     *
     * The gap between the two columns is the interesting number. A drug
     * prescribed far more often than it is dispensed is either out of stock or
     * being filled somewhere else.
     */
    public function drugUsage(Request $request)
    {
        [$from, $to] = $this->range($request, 30);

        $prescribed = PrescriptionMedicine::query()
            ->join('prescription', 'prescription.id', '=', 'prescription_medicine.prescription_id')
            ->whereBetween('prescription.date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('prescription_medicine.name as name, count(*) as n')
            ->groupBy('prescription_medicine.name')
            ->pluck('n', 'name');

        $dispensed = PharmacySaleItem::query()
            ->join('pharmacy_payment', 'pharmacy_payment.id', '=', 'pharmacy_sale_item.pharmacy_payment_id')
            ->whereBetween('pharmacy_payment.date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('pharmacy_sale_item.name as name, sum(pharmacy_sale_item.quantity) as qty, sum(pharmacy_sale_item.line_total) as value')
            ->groupBy('pharmacy_sale_item.name')
            ->get()
            ->keyBy('name');

        $names = $prescribed->keys()->merge($dispensed->keys())->unique()->sort()->values();

        $rows = $names->map(fn ($name) => [
            'name' => $name,
            'prescribed' => (int) ($prescribed[$name] ?? 0),
            'dispensed' => (int) ($dispensed[$name]->qty ?? 0),
            'value' => (float) ($dispensed[$name]->value ?? 0),
        ])->sortByDesc(fn ($r) => $r['dispensed'] + $r['prescribed'])->values();

        return view('app.reports.drug-usage', [
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'totalValue' => $rows->sum('value'),
        ]);
    }

    /**
     * Who owes money, and for how long.
     *
     * The balance comes from invoice_payment rows rather than the legacy single
     * amount_received column, which is what makes a part paid invoice show its
     * real remainder instead of looking either settled or untouched.
     */
    public function debtors(Request $request)
    {
        $invoices = Payment::with('payments')
            ->orderBy('date')
            ->get()
            ->filter(fn ($invoice) => $invoice->balance() > 0.001);

        $patients = Patient::whereIn('id', $invoices->pluck('patient')->filter()->unique())
            ->get(['id', 'name', 'patient_id', 'phone'])->keyBy('id');

        $rows = $invoices->groupBy('patient')->map(function ($group, $patientId) use ($patients) {
            $oldest = $group->min(fn ($i) => $i->date);

            return [
                'patient' => $patients[$patientId] ?? null,
                'invoices' => $group->count(),
                'owed' => round($group->sum(fn ($i) => $i->balance()), 2),
                'oldest' => $oldest,
                'days' => $oldest ? Carbon::parse($oldest)->diffInDays(now()) : null,
            ];
        })->sortByDesc('owed')->values();

        // Ageing buckets, the way a finance office reads a debtor list.
        $buckets = ['0 to 30 days' => 0, '31 to 60 days' => 0, '61 to 90 days' => 0, 'over 90 days' => 0];
        foreach ($rows as $row) {
            $days = $row['days'] ?? 0;
            $key = $days <= 30 ? '0 to 30 days' : ($days <= 60 ? '31 to 60 days' : ($days <= 90 ? '61 to 90 days' : 'over 90 days'));
            $buckets[$key] += $row['owed'];
        }

        return view('app.reports.debtors', [
            'rows' => $rows,
            'total' => round($rows->sum('owed'), 2),
            'buckets' => $buckets,
        ]);
    }

    /**
     * Bed occupancy over a date range, plus length of stay.
     *
     * A bed is occupied when an admission row points at it and d_time is still
     * null, so this counts admissions rather than reading bed.status. The two
     * used to disagree because a clerk set each of them separately.
     */
    public function occupancy(Request $request)
    {
        [$from, $to] = $this->range($request, 30);

        $admissions = AllotedBed::query()
            ->where(function ($q) use ($from) {
                $q->whereNull('d_time')->orWhere('d_time', '>=', $from->toDateString());
            })
            ->where('a_time', '<=', $to->copy()->endOfDay())
            ->get();

        $beds = Bed::all()->keyBy('id');
        $categories = BedCategory::pluck('category', 'id');
        $totalBeds = max(1, $beds->count());

        // One row per day in the range, counting admissions that span it.
        $days = [];
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $endOfDay = $day->copy()->endOfDay();
            $occupied = $admissions->filter(function ($a) use ($day, $endOfDay) {
                $in = $a->a_time ? Carbon::parse($a->a_time) : null;
                $out = $a->d_time ? Carbon::parse($a->d_time) : null;

                return $in && $in->lte($endOfDay) && (! $out || $out->gte($day->copy()->startOfDay()));
            })->count();

            $days[] = [
                'date' => $day->toDateString(),
                'occupied' => $occupied,
                'percent' => round($occupied / $totalBeds * 100),
            ];
        }

        $closed = $admissions->filter(fn ($a) => $a->a_time && $a->d_time);
        $stays = $closed->map(fn ($a) => max(1, Carbon::parse($a->a_time)->diffInDays(Carbon::parse($a->d_time))));

        $byWard = $admissions->groupBy(function ($a) use ($beds, $categories) {
            $bed = $beds[$a->bed_id] ?? null;

            return $bed ? ($categories[$bed->category] ?? 'Uncategorised') : 'Removed bed';
        })->map->count()->sortDesc();

        return view('app.reports.occupancy', [
            'days' => $days,
            'from' => $from,
            'to' => $to,
            'totalBeds' => $beds->count(),
            'currentlyOccupied' => $admissions->whereNull('d_time')->count(),
            'averageStay' => $stays->count() ? round($stays->avg(), 1) : null,
            'discharges' => $closed->count(),
            'byWard' => $byWard,
            'peak' => collect($days)->max('occupied'),
        ]);
    }

    /**
     * Date range from the query string, defaulting to the last N days.
     *
     * Reversed dates are swapped rather than rejected, because typing them the
     * wrong way round is the single most common thing people do with a pair of
     * date boxes and an error message helps nobody.
     */
    private function range(Request $request, $defaultDays)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $from = $from ? Carbon::parse($from)->startOfDay() : now()->copy()->subDays($defaultDays)->startOfDay();
        $to = $to ? Carbon::parse($to)->startOfDay() : now()->copy()->startOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        // A range is capped so a year of day rows cannot be asked for by accident.
        if ($from->diffInDays($to) > 366) {
            $from = $to->copy()->subDays(366);
        }

        return [$from, $to];
    }
}
