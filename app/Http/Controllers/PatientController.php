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

use App\Models\AllotedBed;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Doctor;
use App\Models\Lab;
use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\Payment;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    ->orWhere('cnic', 'like', $like)
                    ->orWhere('old_mrn', 'like', $like)
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
            'patientTypes' => PatientType::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['add_date'] = now()->toDateString();
        $data['registration_time'] = now();
        $data['how_added'] = 'front desk';
        $data['img_url'] = $this->storePhoto($request) ?? '';

        // Legacy link to the Ion Auth user id from the CodeIgniter system. The
        // column is NOT NULL with no default, and the old application wrote an
        // empty string when a patient had no login, so that convention is kept.
        $data['ion_user_id'] = '';

        // The id is derived from the highest one already issued, so reading it and
        // inserting have to be one atomic step. Two clerks registering a walk-in at
        // the same moment would otherwise both read the same number and both use it.
        $patient = DB::transaction(function () use ($data) {
            $data['patient_id'] = $this->nextPatientId();

            return Patient::create($data);
        });

        return redirect('/patients/' . $patient->id)
            ->with('success', 'Patient ' . $patient->patient_id . ' registered.');
    }

    /**
     * The patient chart: demographics plus everything recorded against this patient.
     *
     * Each module keeps its own screens, but a doctor seeing a patient needs the whole
     * record in one place: past visits with vitals, prescriptions, lab results, stays
     * on the ward, history notes and what is still owed. Every list is read live from
     * its own table, so the chart can never disagree with the module that owns the data.
     */
    public function show($id)
    {
        $patient = Patient::findOrFail($id);

        $doctorNames = Doctor::pluck('name', 'id');

        $appointments = Appointment::where('patient', $patient->id)
            ->orderByDesc('date')->orderByDesc('s_time')->get();

        $prescriptions = Prescription::with('items')->where('patient', $patient->id)
            ->orderByDesc('date')->orderByDesc('id')->get();

        $labs = Lab::with('tests')->where('patient', $patient->id)
            ->orderByDesc('date')->orderByDesc('id')->get();

        $admissions = AllotedBed::where('patient', $patient->id)
            ->orderByDesc('a_time')->orderByDesc('id')->get();

        $history = MedicalHistory::where('patient_id', $patient->id)
            ->orderByDesc('date')->orderByDesc('id')->get();

        $invoices = Payment::with('payments')->where('patient', $patient->id)
            ->orderByDesc('date')->orderByDesc('id')->get();

        $billed = round((float) $invoices->sum('gross_total'), 2);
        $paid = round((float) $invoices->sum(fn ($invoice) => $invoice->payments->sum('amount')), 2);

        return view('app.patients.show', [
            'patient' => $patient,
            'doctorNames' => $doctorNames,
            'appointments' => $appointments,
            'prescriptions' => $prescriptions,
            'labs' => $labs,
            'admissions' => $admissions,
            'beds' => Bed::whereIn('id', $admissions->pluck('bed_id')->filter())->get()->keyBy('id'),
            'bedCategories' => BedCategory::pluck('category', 'id'),
            'history' => $history,
            'invoices' => $invoices,
            'billed' => $billed,
            'paid' => $paid,
            'outstanding' => round($billed - $paid, 2),
        ]);
    }

    public function edit($id)
    {
        return view('app.patients.form', [
            'patient' => Patient::findOrFail($id),
            'doctors' => $this->doctors(),
            'bloodGroups' => self::BLOOD_GROUPS,
            'patientTypes' => PatientType::orderBy('name')->get(),
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
            'photo_data' => 'nullable|string|max:3000000',
            'patient_type_id' => 'nullable|integer|exists:patient_type,id',
            'cnic' => ['nullable', 'regex:/^\d{5}-?\d{7}-?\d$/'],
            'guardian_relation' => 'nullable|string|max:40',
            'guardian_mrn' => 'nullable|string|max:20',
            'mlc' => 'nullable|boolean',
            'old_mrn' => 'nullable|string|max:40',
            'employee_no' => 'nullable|string|max:40',
            'alt_phone' => 'nullable|string|max:40',
            'city' => 'nullable|string|max:60',
        ];

        $data = $request->validate($rules, [
            'cnic.regex' => 'A CNIC is 13 digits, written 12345-1234567-1.',
        ]);
        unset($data['photo'], $data['photo_data']);

        // Optional fields the legacy table never had: null when left blank.
        foreach (['cnic', 'guardian_relation', 'guardian_mrn', 'old_mrn', 'employee_no', 'alt_phone', 'city'] as $key) {
            $data[$key] = isset($data[$key]) && trim((string) $data[$key]) !== '' ? trim((string) $data[$key]) : null;
        }
        if ($data['cnic']) {
            $digits = preg_replace('/\D/', '', $data['cnic']);
            $data['cnic'] = substr($digits, 0, 5) . '-' . substr($digits, 5, 7) . '-' . substr($digits, 12, 1);
        }
        $data['mlc'] = (bool) ($data['mlc'] ?? false);
        $data['patient_type_id'] = $data['patient_type_id'] ?? PatientType::defaultId();

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

    /**
     * Health card: a wallet card with the MR number as a barcode, so the desk can
     * find the patient by scanning instead of typing.
     */
    public function card($id)
    {
        $patient = Patient::findOrFail($id);

        return view('app.patients.card', ['patient' => $patient, 'barcode' => $this->barcode($patient->patient_id), 'wristband' => false,
            'type' => PatientType::find($patient->patient_type_id)]);
    }

    /** Wristband for an admitted patient: name, MR number, age, sex, blood group and barcode. */
    public function wristband($id)
    {
        $patient = Patient::findOrFail($id);

        return view('app.patients.card', ['patient' => $patient, 'barcode' => $this->barcode($patient->patient_id), 'wristband' => true,
            'type' => PatientType::find($patient->patient_type_id)]);
    }

    private function barcode($code)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        return 'data:image/png;base64,' . base64_encode($generator->getBarcode((string) $code, $generator::TYPE_CODE_128, 2, 50));
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
     *
     * Must be called inside a transaction. The read takes a lock so a second
     * registration waits rather than reading the same highest value.
     */
    private function nextPatientId()
    {
        $highest = Patient::query()
            ->lockForUpdate()
            ->selectRaw('MAX(CAST(patient_id AS UNSIGNED)) AS n')
            ->value('n');

        return str_pad((string) (((int) $highest) + 1), 6, '0', STR_PAD_LEFT);
    }

    /** Stores an uploaded photo and returns its public path, or null if none was sent. */
    private function storePhoto(Request $request)
    {
        // A photo taken with the desk webcam arrives as a data URL from the form.
        $shot = (string) $request->input('photo_data', '');
        if (! $request->hasFile('photo') && preg_match('#^data:image/(jpeg|png);base64,(.+)$#', $shot, $m)) {
            $bytes = base64_decode($m[2], true);
            if ($bytes !== false && @imagecreatefromstring($bytes) !== false) {
                $path = 'patients/' . bin2hex(random_bytes(16)) . '.' . ($m[1] === 'png' ? 'png' : 'jpg');
                Storage::disk('public')->put($path, $bytes);

                return $path;
            }
        }

        if (! $request->hasFile('photo')) {
            return null;
        }

        return Storage::disk('public')->put('patients', $request->file('photo'));
    }
}
