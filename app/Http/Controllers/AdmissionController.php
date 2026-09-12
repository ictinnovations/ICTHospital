<?php
/**
 * ICTHospital - admissions and discharges.
 *
 * An admission is a row in `alloted_bed`: a patient, a bed, an admit time, and a
 * discharge time that stays null until they leave. Nothing else marks a bed as
 * taken, which is deliberate. The `bed.status` column exists and is kept in step,
 * but the admission record is the truth, so the two cannot disagree the way they
 * did in the CodeIgniter system where a clerk set both by hand.
 *
 * Two rules the original did not enforce: a bed holds one patient at a time, and
 * a patient is admitted to one bed at a time.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\AllotedBed;
use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdmissionController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Current admissions by default; past ones on request. */
    public function index(Request $request)
    {
        $show = $request->input('show', 'current');

        $admissions = AllotedBed::query()
            ->when($show === 'current', fn ($q) => $q->whereNull('d_time'))
            ->when($show === 'discharged', fn ($q) => $q->whereNotNull('d_time'))
            ->orderByDesc('a_time')
            ->limit(200)
            ->get();

        return view('app.admissions.index', [
            'admissions' => $admissions,
            'show' => $show,
            'patients' => Patient::whereIn('id', $admissions->pluck('patient')->filter())->pluck('name', 'id'),
            'beds' => Bed::whereIn('id', $admissions->pluck('bed_id')->filter())->get()->keyBy('id'),
            'categories' => BedCategory::pluck('category', 'id'),
        ]);
    }

    public function create(Request $request)
    {
        return view('app.admissions.form', [
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'freeBeds' => $this->freeBeds(),
            'categories' => BedCategory::pluck('category', 'id'),
            'selectedPatient' => $request->input('patient'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient' => 'required|integer|exists:patient,id',
            'bed_id' => 'required|integer|exists:bed,id',
            'a_time' => 'nullable|date',
        ]);

        $bed = Bed::findOrFail($data['bed_id']);
        // validate() leaves absent nullable keys out of the result entirely, so
        // this has to be a null-coalesce rather than a truthiness check.
        $admitted = ! empty($data['a_time']) ? \Carbon\Carbon::parse($data['a_time']) : now();

        // Both checks run inside the transaction that writes the row, so two
        // clerks admitting to the same bed at once cannot both succeed.
        DB::transaction(function () use ($data, $bed, $admitted) {
            if (AllotedBed::occupied()->where('bed_id', $bed->id)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'bed_id' => 'That bed is already occupied.',
                ]);
            }

            if (AllotedBed::occupied()->where('patient', $data['patient'])->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'patient' => 'That patient is already admitted. Discharge them first.',
                ]);
            }

            AllotedBed::create([
                'patient' => $data['patient'],
                'bed_id' => $bed->id,
                'number' => $bed->number,
                'category' => $bed->category,
                'a_time' => $admitted->toDateTimeString(),
                'd_time' => null,
                'status' => 'admitted',
                'x' => '',
            ]);

            $bed->update(['status' => 'occupied', 'last_a_time' => $admitted->toDateTimeString()]);
        });

        return redirect('/admissions')->with('success', 'Patient admitted.');
    }

    public function show($id)
    {
        $admission = AllotedBed::findOrFail($id);

        return view('app.admissions.show', [
            'admission' => $admission,
            'patient' => Patient::find($admission->patient),
            'bed' => Bed::find($admission->bed_id),
            'categories' => BedCategory::pluck('category', 'id'),
        ]);
    }

    /**
     * Discharge stamps the leaving time and frees the bed.
     *
     * Discharging twice is refused rather than silently overwriting the first
     * discharge time, which would quietly lengthen or shorten a stay.
     */
    public function discharge(Request $request, $id)
    {
        $admission = AllotedBed::findOrFail($id);

        if ($admission->d_time) {
            return redirect('/admissions/' . $admission->id)
                ->with('error', 'That admission was already discharged on ' . $admission->d_time . '.');
        }

        $data = $request->validate(['d_time' => 'nullable|date']);
        $left = ! empty($data['d_time']) ? \Carbon\Carbon::parse($data['d_time']) : now();

        if ($admission->a_time && $left->lt(\Carbon\Carbon::parse($admission->a_time))) {
            throw ValidationException::withMessages([
                'd_time' => 'A patient cannot be discharged before they were admitted.',
            ]);
        }

        DB::transaction(function () use ($admission, $left) {
            $admission->update(['d_time' => $left->toDateTimeString(), 'status' => 'discharged']);

            if ($bed = Bed::find($admission->bed_id)) {
                $bed->update(['status' => 'free', 'last_d_time' => $left->toDateTimeString()]);
            }
        });

        return redirect('/admissions')->with('success', 'Patient discharged.');
    }

    /** Beds with nobody in them, which is the only list worth offering on admission. */
    private function freeBeds()
    {
        $taken = AllotedBed::occupied()->pluck('bed_id')->filter();

        return Bed::whereNotIn('id', $taken->isEmpty() ? [0] : $taken->all())
            ->orderBy('category')
            ->orderBy('number')
            ->get();
    }
}
