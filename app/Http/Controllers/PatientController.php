<?php
/**
 * ICTHospital - patient registration and records.
 *
 * The patient table, model and migration came across from the CodeIgniter system
 * during the rebuild, but nothing drove them: there was no controller and no view,
 * so a hospital could not register a patient at all. This is the front desk entry
 * point that the rest of the clinical modules hang off, since admissions,
 * prescriptions, lab requests and invoices all key on a patient row.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends BaseController
{
    /** Blood groups offered in the form. Free text in the schema, fixed list here. */
    private const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Patient list with a single search box.
     *
     * One box rather than a field-per-column filter, because at a front desk the
     * person on the phone gives you whatever they remember: a name, a number, or
     * the patient ID off a card.
     */
    public function index(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        $patients = Patient::query()
            ->when($term !== '', function ($query) use ($term) {
                $like = '%' . $term . '%';
                $query->where('name', 'like', $like)
                    ->orWhere('patient_id', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('app.patients.index', compact('patients', 'term'));
    }

    public function create()
    {
        return view('app.patients.form', [
            'patient' => new Patient(),
            'doctors' => $this->doctors(),
            'bloodGroups' => self::BLOOD_GROUPS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['patient_id'] = $this->nextPatientId();
        $data['add_date'] = now()->toDateString();
        $data['registration_time'] = now();
        $data['how_added'] = 'front desk';
        $data['img_url'] = $this->storePhoto($request) ?? '';

        // Legacy link to the Ion Auth user id from the CodeIgniter system. The
        // column is NOT NULL with no default, and the old application wrote an
        // empty string when a patient had no login, so that convention is kept.
        $data['ion_user_id'] = '';

        $patient = Patient::create($data);

        return redirect('/patients/' . $patient->id)
            ->with('success', 'Patient ' . $patient->patient_id . ' registered.');
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);

        return view('app.patients.show', compact('patient'));
    }

    public function edit($id)
    {
        return view('app.patients.form', [
            'patient' => Patient::findOrFail($id),
            'doctors' => $this->doctors(),
            'bloodGroups' => self::BLOOD_GROUPS,
        ]);
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $data = $this->validated($request, $patient->id);

        $photo = $this->storePhoto($request);
        if ($photo !== null) {
            $data['img_url'] = $photo;
        }

        $patient->update($data);

        return redirect('/patients/' . $patient->id)->with('success', 'Patient record updated.');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $label = $patient->patient_id . ' ' . $patient->name;
        $patient->delete();

        return redirect('/patients')->with('success', 'Deleted patient ' . $label . '.');
    }

    /**
     * Shared validation.
     *
     * Only name is genuinely required. A walk-in with no email, no address and no
     * date of birth still has to be registerable, otherwise the desk works around
     * the form with junk data and the record is worse than an incomplete one.
     */
    private function validated(Request $request, $ignoreId = null)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'father_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:190',
            'phone' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:100',
            'sex' => 'nullable|in:male,female,other',
            'birthdate' => 'nullable|date|before_or_equal:today',
            'bloodgroup' => 'nullable|in:' . implode(',', self::BLOOD_GROUPS),
            'doctor' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ];

        $data = $request->validate($rules);
        unset($data['photo']);

        foreach (['father_name', 'email', 'phone', 'address', 'sex', 'bloodgroup', 'doctor'] as $key) {
            $data[$key] = $data[$key] ?? '';
        }

        // The schema carries both birthdate and a varchar age. Derive age so the
        // two cannot drift apart, which is what happened in the system this
        // replaces, where age was typed by hand and never updated again.
        $data['age'] = ! empty($data['birthdate'])
            ? (string) \Carbon\Carbon::parse($data['birthdate'])->age
            : '';

        return $data;
    }

    /** Doctors for the "assigned to" select, falling back to an empty list. */
    private function doctors()
    {
        return Doctor::orderBy('name')->pluck('name', 'name');
    }

    /**
     * Sequential patient ID.
     *
     * Derived from the highest existing numeric ID rather than a row count, so
     * deleting a record cannot hand the next patient a number already in use.
     */
    private function nextPatientId()
    {
        $highest = Patient::query()
            ->selectRaw('MAX(CAST(patient_id AS UNSIGNED)) AS n')
            ->value('n');

        return str_pad((string) (((int) $highest) + 1), 6, '0', STR_PAD_LEFT);
    }

    /** Stores an uploaded photo and returns its public path, or null if none was sent. */
    private function storePhoto(Request $request)
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return Storage::disk('public')->put('patients', $request->file('photo'));
    }
}
