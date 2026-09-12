<?php
/**
 * ICTHospital - laboratory requests and results.
 *
 * The lab table came across with `category_name` holding every test on a request
 * as one packed string and `report` holding all the results as another. Neither
 * could be queried, so nobody could ask what was still outstanding.
 *
 * Tests are now rows in lab_test, each with its own result and status, which is
 * what lets a request be part reported: bloods back, culture still growing. The
 * request's own status is derived from its tests rather than set by hand, so the
 * two cannot disagree.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Lab;
use App\Models\LabCategory;
use App\Models\LabTest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LabController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $patientId = $request->input('patient', '');

        $requests = Lab::query()
            ->with('tests')
            ->when($patientId !== '', fn ($q) => $q->where('patient', $patientId))
            ->when($status === 'pending', fn ($q) => $q->whereHas('tests', fn ($t) => $t->whereNull('result')->orWhere('result', '')))
            ->when($status === 'reported', fn ($q) => $q->whereDoesntHave('tests', fn ($t) => $t->whereNull('result')->orWhere('result', '')))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('app.lab.index', [
            'requests' => $requests,
            'status' => $status,
            'patientId' => $patientId,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
        ]);
    }

    public function create(Request $request)
    {
        return view('app.lab.form', [
            'request' => new Lab(['date' => now()->toDateString(), 'patient' => $request->input('patient')]),
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'catalogue' => LabCategory::orderBy('category')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $lab = DB::transaction(function () use ($request) {
            $data = $this->validatedRequest($request);
            $lab = Lab::create($data['fields']);
            $this->attachTests($lab, $data['tests']);

            return $lab;
        });

        return redirect('/lab/' . $lab->id)->with('success', 'Lab request raised.');
    }

    public function show($id)
    {
        $lab = Lab::with('tests')->findOrFail($id);

        return view('app.lab.show', [
            'request' => $lab,
            'patient' => Patient::find($lab->patient),
            'doctor' => Doctor::find($lab->doctor),
        ]);
    }

    /**
     * Records results.
     *
     * Each test is saved independently, so a part reported request is a normal
     * state rather than something the form has to work around.
     */
    public function saveResults(Request $request, $id)
    {
        $lab = Lab::with('tests')->findOrFail($id);

        $input = $request->validate([
            'results' => 'array',
            'results.*' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($lab, $input) {
            foreach ($lab->tests as $test) {
                $value = trim((string) ($input['results'][$test->id] ?? ''));

                // A result that is already recorded is not wiped by a blank box,
                // which is what happens if you submit the form from a stale page.
                if ($value === '' && $test->isReported()) {
                    continue;
                }

                $test->update([
                    'result' => $value === '' ? null : $value,
                    'status' => $value === '' ? 'requested' : 'reported',
                    'reported_at' => $value === '' ? null : now()->toDateTimeString(),
                ]);
            }

            $this->refreshSummary($lab->fresh('tests'));
        });

        return redirect('/lab/' . $lab->id)->with('success', 'Results saved.');
    }

    public function destroy($id)
    {
        $lab = Lab::findOrFail($id);

        DB::transaction(function () use ($lab) {
            $lab->tests()->delete();
            $lab->delete();
        });

        return redirect('/lab')->with('success', 'Lab request removed.');
    }

    private function validatedRequest(Request $request)
    {
        $input = $request->validate([
            'patient' => 'required|integer|exists:patient,id',
            'doctor' => 'nullable|integer|exists:doctor,id',
            'date' => 'required|date',
            'tests' => 'array',
            'tests.*' => 'integer|exists:lab_category,id',
        ]);

        $tests = array_values(array_unique($input['tests'] ?? []));

        if (empty($tests)) {
            throw ValidationException::withMessages([
                'tests' => 'Choose at least one test, or the request asks for nothing.',
            ]);
        }

        $patient = Patient::find($input['patient']);
        $doctor = ! empty($input['doctor']) ? Doctor::find($input['doctor']) : null;

        return [
            'fields' => [
                'patient' => $input['patient'],
                'doctor' => $input['doctor'] ?? null,
                'date' => $input['date'],
                'date_string' => $input['date'],
                // The legacy denormalised columns are filled so old reports and
                // exports that read them keep working.
                'patient_name' => $patient->name ?? null,
                'patient_phone' => $patient->phone ?? null,
                'patient_address' => $patient->address ?? null,
                'doctor_name' => $doctor->name ?? null,
                'category' => null,
                'category_name' => null,
                'report' => null,
                'status' => 'requested',
                'user' => (string) (auth()->id() ?? ''),
            ],
            'tests' => $tests,
        ];
    }

    private function attachTests(Lab $lab, array $categoryIds)
    {
        $catalogue = LabCategory::whereIn('id', $categoryIds)->get()->keyBy('id');

        foreach ($categoryIds as $categoryId) {
            $category = $catalogue->get($categoryId);

            if (! $category) {
                continue;
            }

            LabTest::create([
                'lab_id' => $lab->id,
                'lab_category_id' => $category->id,
                'name' => $category->category,
                'reference_value' => $category->reference_value,
                'result' => null,
                'status' => 'requested',
            ]);
        }

        $this->refreshSummary($lab->fresh('tests'));
    }

    /**
     * Rewrites the legacy summary columns and the request status.
     *
     * The status is derived: nothing back is "requested", some back is "partial",
     * everything back is "reported". Nobody sets it by hand, so it cannot drift
     * away from the results the way it did in the system this replaces.
     */
    private function refreshSummary(Lab $lab)
    {
        $tests = $lab->tests;
        $reported = $tests->filter->isReported()->count();

        $status = $reported === 0 ? 'requested'
            : ($reported === $tests->count() ? 'reported' : 'partial');

        $lab->update([
            'category' => $tests->pluck('lab_category_id')->filter()->implode(','),
            'category_name' => substr($tests->pluck('name')->implode(', '), 0, 1000),
            'report' => substr($tests->map->summary()->implode("\n"), 0, 10000),
            'status' => $status,
            'report_date' => $status === 'reported' ? now()->toDateTimeString() : null,
        ]);
    }
}
