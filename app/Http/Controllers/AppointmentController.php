<?php
/**
 * ICTHospital - appointment booking.
 *
 * The appointment table and model came across in the rebuild and the
 * hospital:appointment-reminder cron already reads them, but nothing could
 * create a row, so the reminder had nothing to remind anyone about.
 *
 * The one rule this enforces that the original system did not: a doctor cannot
 * hold two appointments that overlap. The CodeIgniter version booked whatever it
 * was given and left the clash for the front desk to discover on the day.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentController extends BaseController
{
    public const STATUSES = ['scheduled', 'arrived', 'completed', 'cancelled', 'no show'];

    /** Default consultation length, in minutes, when no end time is given. */
    private const DEFAULT_MINUTES = 15;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Day view, because that is how a front desk actually works: one doctor,
     * one day, in time order. The date defaults to today rather than showing
     * every appointment ever booked.
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $doctorId = $request->input('doctor', '');
        $status = $request->input('status', '');

        $appointments = Appointment::query()
            ->when($date !== '', fn ($q) => $q->whereDate('date', $date))
            ->when($doctorId !== '', fn ($q) => $q->where('doctor', $doctorId))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('s_time')
            ->get();

        return view('app.appointments.index', [
            'appointments' => $appointments,
            'date' => $date,
            'doctorId' => $doctorId,
            'status' => $status,
            'doctors' => $this->doctors(),
            'patients' => $this->patientNames($appointments->pluck('patient')),
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(Request $request)
    {
        $appointment = new Appointment([
            'date' => $request->input('date', now()->toDateString()),
            'patient' => $request->input('patient'),
            'status' => 'scheduled',
        ]);

        return view('app.appointments.form', $this->formData($appointment));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $appointment = Appointment::create($data);

        return redirect('/appointments?date=' . $appointment->date)
            ->with('success', 'Appointment booked.');
    }

    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);

        return view('app.appointments.show', [
            'appointment' => $appointment,
            'patient' => Patient::find($appointment->patient),
            'doctor' => Doctor::find($appointment->doctor),
        ]);
    }

    public function edit($id)
    {
        return view('app.appointments.form', $this->formData(Appointment::findOrFail($id)));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($this->validated($request, $appointment->id));

        return redirect('/appointments/' . $appointment->id)->with('success', 'Appointment updated.');
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $date = $appointment->date;
        $appointment->delete();

        return redirect('/appointments?date=' . $date)->with('success', 'Appointment removed.');
    }

    /**
     * Validation, plus the derived time fields and the clash check.
     *
     * s_time and e_time are built from the date and the start time rather than
     * asked for separately, so the three can never disagree. s_time_key is the
     * legacy sort column and is filled from the same source.
     */
    private function validated(Request $request, $ignoreId = null)
    {
        $input = $request->validate([
            'patient' => 'required|integer|exists:patient,id',
            'doctor' => 'required|integer|exists:doctor,id',
            'date' => 'required|date',
            'start' => 'required|date_format:H:i',
            'minutes' => 'nullable|integer|min:5|max:480',
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'remarks' => 'nullable|string|max:500',
            'b_p' => 'nullable|string|max:100',
            'pulse' => 'nullable|string|max:100',
            'temprature' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:100',
        ]);

        $minutes = (int) ($input['minutes'] ?: self::DEFAULT_MINUTES);
        $start = Carbon::parse($input['date'] . ' ' . $input['start']);
        $end = (clone $start)->addMinutes($minutes);

        $this->assertNoClash($input['doctor'], $start, $end, $ignoreId);

        return [
            'patient' => $input['patient'],
            'doctor' => $input['doctor'],
            'date' => $start->toDateString(),
            'time_slot' => $start->format('H:i') . ' - ' . $end->format('H:i'),
            's_time' => $start->toDateTimeString(),
            'e_time' => $end->toDateTimeString(),
            's_time_key' => $start->format('YmdHi'),
            'status' => $input['status'],
            'remarks' => $input['remarks'] ?? '',
            'b_p' => $input['b_p'] ?? null,
            'pulse' => $input['pulse'] ?? null,
            'temprature' => $input['temprature'] ?? null,
            'weight' => $input['weight'] ?? null,
            'add_date' => now()->toDateString(),
            'registration_time' => now()->toDateTimeString(),
            'user' => (string) (auth()->id() ?? ''),
            'request' => '',
        ];
    }

    /**
     * Refuses a booking that overlaps one the doctor already has.
     *
     * Cancelled and no-show appointments are ignored, since the slot is free
     * again. Touching ends do not count as a clash: 09:00-09:15 and 09:15-09:30
     * are back to back, which is the normal way a clinic runs.
     */
    private function assertNoClash($doctorId, Carbon $start, Carbon $end, $ignoreId = null)
    {
        $clash = Appointment::query()
            ->where('doctor', $doctorId)
            ->whereNotIn('status', ['cancelled', 'no show'])
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->where('s_time', '<', $end->toDateTimeString())
            ->where('e_time', '>', $start->toDateTimeString())
            ->first();

        if (! $clash) {
            return;
        }

        $patient = Patient::find($clash->patient);

        throw ValidationException::withMessages([
            'start' => sprintf(
                'That doctor is already booked %s with %s.',
                $clash->time_slot,
                $patient ? $patient->name : 'another patient'
            ),
        ]);
    }

    private function formData(Appointment $appointment)
    {
        return [
            'appointment' => $appointment,
            'doctors' => $this->doctors(),
            'patientList' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'statuses' => self::STATUSES,
        ];
    }

    private function doctors()
    {
        return Doctor::orderBy('name')->get(['id', 'name']);
    }

    /** Patient id => name, for the list view, in one query rather than per row. */
    private function patientNames($ids)
    {
        return Patient::whereIn('id', $ids->filter()->unique())->pluck('name', 'id');
    }
}
