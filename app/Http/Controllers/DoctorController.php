<?php
/**
 * ICTHospital - the doctor register.
 *
 * Appointments, prescriptions, lab requests and invoices all pick a doctor from
 * this table, and until now there was no way to put one in it. A clean install
 * had an empty doctor list, so every one of those screens offered an empty select
 * and the clinical modules could not be used at all without seeding rows by hand.
 *
 * Department is free text with a suggestion list built from the doctors already
 * on file, rather than a foreign key. The department table exists but has no
 * screen of its own, and forcing a lookup that cannot be filled in would recreate
 * the problem this controller is here to solve.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DoctorController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        $doctors = Doctor::query()
            ->when($term !== '', function ($query) use ($term) {
                $like = '%' . $term . '%';
                $query->where('name', 'like', $like)
                    ->orWhere('department', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        // Upcoming load per doctor, so the list answers the question the person
        // looking at it actually has: who is free this week.
        $upcoming = Appointment::query()
            ->whereDate('date', '>=', now()->toDateString())
            ->whereNotIn('status', ['cancelled', 'no show'])
            ->selectRaw('doctor, count(*) as n')
            ->groupBy('doctor')
            ->pluck('n', 'doctor');

        return view('app.doctors.index', compact('doctors', 'term', 'upcoming'));
    }

    public function create()
    {
        return view('app.doctors.form', [
            'doctor' => new Doctor(),
            'departments' => $this->departments(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['img_url'] = $this->storePhoto($request) ?? '';

        $doctor = Doctor::create($data);

        return redirect('/doctors/' . $doctor->id)
            ->with('success', $doctor->name . ' added to the register.');
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);

        $appointments = Appointment::where('doctor', $doctor->id)
            ->orderByDesc('date')
            ->orderByDesc('s_time')
            ->limit(20)
            ->get();

        $patients = Patient::whereIn('id', $appointments->pluck('patient')->filter()->unique())
            ->get(['id', 'name', 'patient_id'])
            ->keyBy('id');

        return view('app.doctors.show', compact('doctor', 'appointments', 'patients'));
    }

    public function edit($id)
    {
        return view('app.doctors.form', [
            'doctor' => Doctor::findOrFail($id),
            'departments' => $this->departments(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        $data = $this->validated($request, $doctor->id);

        $photo = $this->storePhoto($request);
        if ($photo !== null) {
            $data['img_url'] = $photo;
        }

        $doctor->update($data);

        return redirect('/doctors/' . $doctor->id)->with('success', 'Doctor record updated.');
    }

    /**
     * A doctor with appointments against them stays on the register.
     *
     * The appointment table holds a doctor id, not a copy of the name, so
     * deleting the row would leave every past appointment pointing at nothing.
     * The same reasoning as the lab and medicine catalogues.
     */
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        if (Appointment::where('doctor', $doctor->id)->exists()) {
            return redirect('/doctors')
                ->with('error', $doctor->name . ' has appointments on file, so the record stays.');
        }

        $name = $doctor->name;
        $doctor->delete();

        return redirect('/doctors')->with('success', 'Removed ' . $name . '.');
    }

    /**
     * Shared validation.
     *
     * Only the name is required, for the same reason the patient form asks for
     * so little: a locum starting on Monday has a name and nothing else filled
     * in yet, and a form that refuses the record gets worked around with junk.
     * Email is unique when given, because it is the closest thing this schema
     * has to an identifier for a person.
     */
    private function validated(Request $request, $ignoreId = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'profile' => 'nullable|string|max:100',
            'procedure_id' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        unset($data['photo']);

        if (! empty($data['email'])) {
            $clash = Doctor::where('email', $data['email'])
                ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
                ->exists();

            if ($clash) {
                throw ValidationException::withMessages([
                    'email' => 'Another doctor is already on file with that email address.',
                ]);
            }
        }

        // Legacy NOT NULL filler columns. The CodeIgniter application wrote empty
        // strings rather than nulls, and x and y are unused layout leftovers that
        // still have no default, so they are filled the same way the old app did.
        foreach (['email', 'phone', 'address', 'department', 'profile'] as $key) {
            $data[$key] = $data[$key] ?? '';
        }

        $data['procedure_id'] = $data['procedure_id'] ?? null;
        $data['ion_user_id'] = $data['ion_user_id'] ?? '';
        $data['x'] = '';
        $data['y'] = '';

        return $data;
    }

    /** Departments already in use, for the form's suggestion list. */
    private function departments()
    {
        return Doctor::query()
            ->whereNotNull('department')
            ->where('department', '<>', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');
    }

    /** Stores an uploaded photo and returns its public path, or null if none was sent. */
    private function storePhoto(Request $request)
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return Storage::disk('public')->put('doctors', $request->file('photo'));
    }
}
