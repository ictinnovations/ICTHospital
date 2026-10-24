<?php
/**
 * ICTHospital - the OPD consultation screen.
 *
 * One page per visit: vitals, complaint, history, examination by system, diagnosis,
 * plan and follow-up, the specialty sections, and orders (lab tests now, a
 * prescription one click away). Allergies and past history carry forward from the
 * patient's previous consultation, because a doctor should not have to ask a known
 * diabetic whether they are diabetic at every visit.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ConsultTemplate;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\LabCategory;
use App\Models\Patient;
use App\Support\LabOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $date = (string) ($request->input('date') ?: now()->toDateString());
        $doctorId = (string) $request->input('doctor', '');

        $rows = Consultation::whereDate('date', $date)
            ->when($doctorId !== '', fn ($q) => $q->where('doctor_id', $doctorId))
            ->orderByDesc('id')->get();

        return view('app.consultations.index', [
            'rows' => $rows, 'date' => $date, 'doctorId' => $doctorId,
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'doctorNames' => Doctor::pluck('name', 'id'),
            'patients' => Patient::whereIn('id', $rows->pluck('patient_id'))->get()->keyBy('id'),
        ]);
    }

    /** The consultation for a visit, started on first open. */
    public function visit($appointmentId)
    {
        $visit = Appointment::findOrFail($appointmentId);
        $patient = Patient::findOrFail($visit->patient);

        $c = Consultation::firstOrNew(['appointment_id' => $visit->id]);
        if (! $c->exists) {
            $last = Consultation::where('patient_id', $patient->id)->orderByDesc('date')->orderByDesc('id')->first();
            $c->fill([
                'patient_id' => $patient->id, 'doctor_id' => $visit->doctor, 'date' => $visit->date ?: now()->toDateString(),
                'allergies' => optional($last)->allergies, 'past_history' => optional($last)->past_history,
                'social_history' => optional($last)->social_history, 'immunizations' => optional($last)->immunizations, 'status' => 'draft',
            ]);
        }

        $orders = \App\Models\Lab::with('tests')->where('patient', $patient->id)->whereDate('date', $c->date ?: now()->toDateString())->get();

        return view('app.consultations.form', [
            'c' => $c, 'visit' => $visit, 'patient' => $patient,
            'doctor' => Doctor::find($visit->doctor),
            'labCatalogue' => LabCategory::orderBy('category')->get(['id', 'category']),
            'templates' => ConsultTemplate::where(fn ($q) => $q->whereNull('doctor_id')->orWhere('doctor_id', $visit->doctor))->orderBy('name')->get(),
            'previous' => Consultation::where('patient_id', $patient->id)->where('id', '!=', (int) $c->id)->orderByDesc('date')->limit(5)->get(),
            'orders' => $orders,
        ]);
    }

    public function save(Request $request, $appointmentId)
    {
        $visit = Appointment::findOrFail($appointmentId);
        $patient = Patient::findOrFail($visit->patient);

        $data = $request->validate([
            'presenting_complaint' => 'nullable|string|max:5000', 'history' => 'nullable|string|max:10000',
            'provisional_diagnosis' => 'nullable|string|max:2000', 'diagnosis' => 'nullable|string|max:2000',
            'plan' => 'nullable|string|max:5000', 'treatment' => 'nullable|string|max:5000',
            'follow_up_date' => 'nullable|date|after_or_equal:today', 'follow_up_notes' => 'nullable|string|max:255',
            'allergies' => 'nullable|string|max:2000',
            'exam' => 'array', 'exam.*.status' => 'nullable|in:not_examined,normal,abnormal', 'exam.*.note' => 'nullable|string|max:1000',
            'past' => 'array', 'past.*' => 'nullable|string|max:200',
            'social' => 'array', 'social.*' => 'nullable|string|max:200',
            'gynae' => 'array', 'gynae.*' => 'nullable|string|max:200',
            'dental' => 'array', 'dental.*' => 'nullable|string|max:2000',
            'eye' => 'array', 'eye.*' => 'nullable|string|max:50',
            'vaccines' => 'array', 'vaccines.*' => 'nullable|date',
            'b_p' => 'nullable|string|max:100', 'pulse' => 'nullable|string|max:100',
            'temprature' => 'nullable|string|max:100', 'weight' => 'nullable|string|max:100',
            'lab_tests' => 'array', 'lab_tests.*' => 'integer|exists:lab_category,id',
            'action' => 'nullable|in:save,complete',
            'template_name' => 'nullable|string|max:100',
        ]);

        $clean = fn ($arr) => array_filter((array) $arr, fn ($v) => $v !== null && $v !== '' && $v !== []);

        $gynae = $clean($data['gynae'] ?? []);
        if (! empty($gynae['lmp'])) {
            [$gynae['edd'], $gynae['gestational_age']] = Consultation::obstetricDates($gynae['lmp']);
        }

        $message = DB::transaction(function () use ($visit, $patient, $data, $clean, $gynae) {
            $c = Consultation::firstOrNew(['appointment_id' => $visit->id]);
            $c->fill([
                'patient_id' => $patient->id, 'doctor_id' => $visit->doctor, 'date' => $c->date ?: ($visit->date ?: now()->toDateString()),
                'presenting_complaint' => $data['presenting_complaint'] ?? null, 'history' => $data['history'] ?? null,
                'provisional_diagnosis' => $data['provisional_diagnosis'] ?? null, 'diagnosis' => $data['diagnosis'] ?? null,
                'plan' => $data['plan'] ?? null, 'treatment' => $data['treatment'] ?? null,
                'follow_up_date' => $data['follow_up_date'] ?? null, 'follow_up_notes' => $data['follow_up_notes'] ?? null,
                'allergies' => $data['allergies'] ?? null,
                'examination' => array_filter(array_map($clean, $data['exam'] ?? [])) ?: null,
                'past_history' => $clean($data['past'] ?? []) ?: null,
                'social_history' => $clean($data['social'] ?? []) ?: null,
                'specialty' => array_filter(['gynae' => $gynae, 'dental' => $clean($data['dental'] ?? []), 'eye' => $clean($data['eye'] ?? [])]) ?: null,
                'immunizations' => $clean($data['vaccines'] ?? []) ?: null,
                'created_by' => $c->created_by ?: auth()->id(),
            ]);
            $c->save();

            $visit->update([
                'b_p' => $data['b_p'] ?? $visit->b_p, 'pulse' => $data['pulse'] ?? $visit->pulse,
                'temprature' => $data['temprature'] ?? $visit->temprature, 'weight' => $data['weight'] ?? $visit->weight,
            ]);

            $msg = 'Consultation saved.';
            if (! empty($data['lab_tests'])) {
                $lab = LabOrder::create($patient, $visit->doctor, $data['lab_tests']);
                if ($lab) {
                    $msg .= ' Lab request #' . $lab->id . ' raised.';
                }
            }

            if (! empty($data['template_name'])) {
                ConsultTemplate::create([
                    'name' => $data['template_name'], 'doctor_id' => $visit->doctor,
                    'fields' => array_intersect_key($c->only(Consultation::TEMPLATE_FIELDS), array_flip(Consultation::TEMPLATE_FIELDS)),
                ]);
                $msg .= ' Template "' . $data['template_name'] . '" saved.';
            }

            if (($data['action'] ?? 'save') === 'complete') {
                $c->update(['status' => 'completed', 'completed_at' => now()]);
                $visit->update(['status' => 'completed', 'checked_out_at' => $visit->checked_out_at ?: now(),
                    'consult_started_at' => $visit->consult_started_at ?: now()]);
                $msg = str_replace('Consultation saved.', 'Consultation completed and the patient checked out.', $msg);
            } elseif (in_array($visit->status, ['scheduled', 'arrived'], true)) {
                // Opening the note and saving it means the patient is with the doctor.
                $visit->update(['status' => 'in consultation', 'consult_started_at' => $visit->consult_started_at ?: now(),
                    'checked_in_at' => $visit->checked_in_at ?: now()]);
            }

            return $msg;
        });

        return redirect('/consultations/visit/' . $visit->id)->with('success', $message);
    }

    public function printout($id)
    {
        $c = Consultation::findOrFail($id);

        return view('app.consultations.print', [
            'c' => $c, 'patient' => Patient::find($c->patient_id), 'doctor' => Doctor::find($c->doctor_id),
            'visit' => $c->appointment_id ? Appointment::find($c->appointment_id) : null,
        ]);
    }

    public function destroyTemplate($id)
    {
        ConsultTemplate::findOrFail($id)->delete();

        return back()->with('success', 'Template removed.');
    }
}
