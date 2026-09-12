<?php
/**
 * ICTHospital - prescriptions.
 *
 * The prescription table came across in the rebuild with a varchar(1000)
 * `medicine` column that the old application packed the whole drug list into as
 * one string. Nothing could be asked of it: not which patients are on a drug that
 * has just been recalled, not what the pharmacy has to dispense today.
 *
 * Prescribed drugs are now rows in prescription_medicine. The legacy column is
 * still written with a readable summary so anything reading it keeps working.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrescriptionController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $patientId = $request->input('patient', '');
        $doctorId = $request->input('doctor', '');
        $drug = trim((string) $request->input('drug', ''));

        $prescriptions = Prescription::query()
            ->with('items')
            ->when($patientId !== '', fn ($q) => $q->where('patient', $patientId))
            ->when($doctorId !== '', fn ($q) => $q->where('doctor', $doctorId))
            // This is the query the old free text column made impossible.
            ->when($drug !== '', fn ($q) => $q->whereHas('items', fn ($i) => $i->where('name', 'like', '%' . $drug . '%')))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('app.prescriptions.index', [
            'prescriptions' => $prescriptions,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'patientId' => $patientId,
            'doctorId' => $doctorId,
            'drug' => $drug,
        ]);
    }

    public function create(Request $request)
    {
        return view('app.prescriptions.form', $this->formData(new Prescription([
            'date' => now()->toDateString(),
            'patient' => $request->input('patient'),
        ])));
    }

    public function store(Request $request)
    {
        $prescription = DB::transaction(function () use ($request) {
            [$fields, $items] = $this->validated($request);
            $prescription = Prescription::create($fields);
            $this->syncItems($prescription, $items);

            return $prescription;
        });

        return redirect('/prescriptions/' . $prescription->id)->with('success', 'Prescription saved.');
    }

    public function show($id)
    {
        $prescription = Prescription::with('items')->findOrFail($id);

        return view('app.prescriptions.show', [
            'prescription' => $prescription,
            'patient' => Patient::find($prescription->patient),
            'doctor' => Doctor::find($prescription->doctor),
        ]);
    }

    public function edit($id)
    {
        return view('app.prescriptions.form', $this->formData(Prescription::with('items')->findOrFail($id)));
    }

    public function update(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        DB::transaction(function () use ($request, $prescription) {
            [$fields, $items] = $this->validated($request);
            $prescription->update($fields);
            $this->syncItems($prescription, $items);
        });

        return redirect('/prescriptions/' . $prescription->id)->with('success', 'Prescription updated.');
    }

    public function destroy($id)
    {
        $prescription = Prescription::findOrFail($id);

        DB::transaction(function () use ($prescription) {
            $prescription->items()->delete();
            $prescription->delete();
        });

        return redirect('/prescriptions')->with('success', 'Prescription removed.');
    }

    /**
     * Validation, split into the prescription fields and its line items.
     *
     * A prescription with no drugs on it is refused. In the old system that was
     * possible and produced a record nobody could act on.
     */
    private function validated(Request $request)
    {
        $input = $request->validate([
            'patient' => 'required|integer|exists:patient,id',
            'doctor' => 'required|integer|exists:doctor,id',
            'date' => 'required|date',
            'symptom' => 'nullable|string|max:100',
            'advice' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:1000',
            'validity' => 'nullable|string|max:100',
            'items' => 'array',
            'items.*.medicine_id' => 'nullable|integer|exists:medicine,id',
            'items.*.name' => 'nullable|string|max:200',
            'items.*.dosage' => 'nullable|string|max:100',
            'items.*.duration' => 'nullable|string|max:100',
            'items.*.instructions' => 'nullable|string|max:500',
        ]);

        $items = $this->cleanItems($input['items'] ?? []);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one medicine, or this prescription says nothing.',
            ]);
        }

        return [[
            'patient' => $input['patient'],
            'doctor' => $input['doctor'],
            'date' => $input['date'],
            'symptom' => $input['symptom'] ?? null,
            'advice' => $input['advice'] ?? null,
            'note' => $input['note'] ?? null,
            'validity' => $input['validity'] ?? null,
            'state' => 'issued',
            'dd' => null,
            'medicine' => null, // filled from the line items once they are known
        ], $items];
    }

    /**
     * Drops blank rows and resolves each line to a name.
     *
     * A drug can be picked from the catalogue or typed in free, because a doctor
     * writing something the hospital does not stock should not be blocked.
     */
    private function cleanItems(array $rows)
    {
        $catalogue = Medicine::pluck('name', 'id');
        $out = [];

        foreach ($rows as $row) {
            $id = $row['medicine_id'] ?? null;
            $name = trim((string) ($row['name'] ?? ''));

            if ($id && $catalogue->has($id)) {
                $name = $catalogue[$id];
            }

            if ($name === '') {
                continue;
            }

            $out[] = [
                'medicine_id' => $id ?: null,
                'name' => $name,
                'dosage' => $this->blankToNull($row['dosage'] ?? null),
                'duration' => $this->blankToNull($row['duration'] ?? null),
                'instructions' => $this->blankToNull($row['instructions'] ?? null),
            ];
        }

        return $out;
    }

    private function blankToNull($value)
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /** Replaces the line items wholesale, then refreshes the legacy summary. */
    private function syncItems(Prescription $prescription, array $items)
    {
        $prescription->items()->delete();

        foreach ($items as $item) {
            $prescription->items()->create($item);
        }

        $prescription->update([
            'medicine' => substr(
                $prescription->items()->get()->map->summary()->implode(', '),
                0,
                1000
            ),
        ]);
    }

    private function formData(Prescription $prescription)
    {
        return [
            'prescription' => $prescription,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'medicines' => Medicine::orderBy('name')->get(['id', 'name', 'generic']),
        ];
    }
}
