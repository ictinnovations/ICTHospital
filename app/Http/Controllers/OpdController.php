<?php
/**
 * ICTHospital - outpatient department: token queue, walk-ins, visit fees, OPD
 * history, doctor booking slots.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\Patient;
use App\Models\PaymentCategory;
use App\Models\SlotBlock;
use App\Models\VisitFee;
use App\Support\Billing;
use App\Support\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpdController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ---------------------------------------------------------------- queue */

    public function queue(Request $request)
    {
        $date = (string) ($request->input('date') ?: now()->toDateString());
        // A doctor's own login opens on their own queue.
        $doctorId = (string) ($request->has('doctor') ? $request->input('doctor', '') : (string) (Doctor::where('ion_user_id', (string) auth()->id())->value('id') ?? ''));

        $visits = Appointment::whereDate('date', $date)
            ->when($doctorId !== '', fn ($q) => $q->where('doctor', $doctorId))
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->sortBy(fn ($a) => [$a->token_no === null ? 1 : 0, (int) $a->token_no, $a->s_time])
            ->values();

        return view('app.opd.queue', [
            'visits' => $visits,
            'date' => $date,
            'doctorId' => $doctorId,
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'doctorNames' => Doctor::pluck('name', 'id'),
            'patients' => Patient::whereIn('id', $visits->pluck('patient'))->get()->keyBy('id'),
            'patientList' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'counts' => [
                'total' => $visits->count(),
                'waiting' => $visits->where('status', 'arrived')->count(),
                'in' => $visits->where('status', 'in consultation')->count(),
                'done' => $visits->where('status', 'completed')->count(),
            ],
            'visitTypes' => VisitFee::TYPES,
        ]);
    }

    /** Check in: the patient is at the desk. Gives the next token for that doctor that day. */
    public function checkIn($id)
    {
        DB::transaction(function () use ($id) {
            $a = Appointment::lockForUpdate()->findOrFail($id);
            if ($a->token_no === null) {
                $a->token_no = $this->nextToken($a->doctor, $a->date);
            }
            $a->checked_in_at = $a->checked_in_at ?: now();
            if (in_array($a->status, ['scheduled', 'no show'], true)) {
                $a->status = 'arrived';
            }
            $a->save();
        });

        return back()->with('success', 'Checked in.');
    }

    public function callIn($id)
    {
        $a = Appointment::findOrFail($id);
        $a->update(['status' => 'in consultation', 'consult_started_at' => now(), 'checked_in_at' => $a->checked_in_at ?: now()]);

        return back()->with('success', 'Called in.');
    }

    public function done($id)
    {
        $a = Appointment::findOrFail($id);
        $a->update(['status' => 'completed', 'checked_out_at' => now(), 'consult_started_at' => $a->consult_started_at ?: now()]);

        return back()->with('success', 'Visit completed.');
    }

    /** A walk-in: booked, checked in and given a token in one step, optionally billed. */
    public function walkIn(Request $request)
    {
        $data = $request->validate([
            'patient' => 'required|integer|exists:patient,id',
            'doctor' => 'required|integer|exists:doctor,id',
            'visit_type' => 'required|in:' . implode(',', array_keys(VisitFee::TYPES)),
            'bill' => 'nullable|boolean',
        ]);

        $visit = DB::transaction(function () use ($data) {
            $start = now()->second(0);
            $end = $start->copy()->addMinutes(15);

            return Appointment::create([
                'patient' => $data['patient'], 'doctor' => $data['doctor'], 'date' => $start->toDateString(),
                'time_slot' => $start->format('H:i') . ' - ' . $end->format('H:i'), 's_time' => $start->toDateTimeString(),
                'e_time' => $end->toDateTimeString(), 's_time_key' => $start->format('YmdHi'), 'status' => 'arrived',
                'remarks' => 'Walk-in', 'add_date' => now()->toDateString(), 'registration_time' => now()->toDateTimeString(),
                'user' => (string) auth()->id(), 'request' => '', 'visit_type' => $data['visit_type'],
                'token_no' => $this->nextToken($data['doctor'], $start->toDateString()), 'checked_in_at' => now(),
            ]);
        });

        $msg = 'Token ' . $visit->token_no . ' issued.';
        if (! empty($data['bill'])) {
            $msg .= ' ' . $this->billFor($visit);
        }

        return redirect('/opd/queue?date=' . $visit->date . '&doctor=' . $visit->doctor)->with('success', $msg);
    }

    public function billVisit(Request $request, $id)
    {
        $visit = Appointment::findOrFail($id);
        if ($request->filled('visit_type') && array_key_exists($request->input('visit_type'), VisitFee::TYPES)) {
            $visit->update(['visit_type' => $request->input('visit_type')]);
        }

        return back()->with('success', $this->billFor($visit->fresh()));
    }

    public function token($id)
    {
        $visit = Appointment::findOrFail($id);

        return view('app.opd.token', [
            'visit' => $visit,
            'patient' => Patient::find($visit->patient),
            'doctor' => Doctor::find($visit->doctor),
            'ahead' => Appointment::where('doctor', $visit->doctor)->whereDate('date', $visit->date)
                ->where('status', 'arrived')->where('token_no', '<', (int) $visit->token_no)->count(),
        ]);
    }

    /** OPD history: tokens, check in and out, waiting and treatment time, payment. */
    public function history(Request $request)
    {
        $from = (string) ($request->input('from') ?: now()->toDateString());
        $to = (string) ($request->input('to') ?: $from);
        $doctorId = (string) $request->input('doctor', '');
        $status = (string) $request->input('status', '');

        $visits = Appointment::whereDate('date', '>=', $from)->whereDate('date', '<=', $to)
            ->when($doctorId !== '', fn ($q) => $q->where('doctor', $doctorId))
            ->when($status === 'active', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->when($status === 'cancelled', fn ($q) => $q->where('status', 'cancelled'))
            ->orderBy('date')->orderBy('token_no')->orderBy('s_time')->get();

        $invoices = \App\Models\Payment::with('payments')->whereIn('id', $visits->pluck('payment_id')->filter())->get()->keyBy('id');

        return view('app.opd.history', [
            'visits' => $visits,
            'patients' => Patient::whereIn('id', $visits->pluck('patient'))->get()->keyBy('id'),
            'doctorNames' => Doctor::pluck('name', 'id'),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'invoices' => $invoices,
            'filters' => compact('from', 'to', 'doctorId', 'status'),
        ]);
    }

    private function nextToken($doctorId, $date)
    {
        $max = Appointment::where('doctor', $doctorId)->whereDate('date', Carbon::parse($date)->toDateString())->lockForUpdate()->max('token_no');

        return ((int) $max) + 1;
    }

    private function billFor(Appointment $visit)
    {
        if ($visit->payment_id) {
            return 'The visit fee is already billed on invoice #' . $visit->payment_id . '.';
        }

        $type = $visit->visit_type ?: 'first';
        $fee = VisitFee::resolve($visit->doctor, $type, Carbon::parse($visit->s_time ?: now()));
        if (! $fee || ! ($service = PaymentCategory::find($fee->payment_category_id))) {
            return 'No visit fee is set up for ' . (VisitFee::TYPES[$type] ?? $type) . ' with this doctor. Set one under OPD, Visit fees.';
        }

        $patient = Patient::findOrFail($visit->patient);
        $invoice = DB::transaction(function () use ($patient, $visit, $service, $fee) {
            $invoice = Billing::raise($patient, $visit->doctor, [['service' => $service, 'price' => $fee->fee]], 'opd', 'OPD visit, token ' . $visit->token_no);
            $visit->update(['payment_id' => $invoice->id]);

            return $invoice;
        });

        return 'Visit fee billed on invoice #' . $invoice->id . ' (' . number_format((float) $invoice->gross_total, 2) . ').';
    }

    /* ---------------------------------------------------------------- visit fees */

    public function fees()
    {
        return view('app.opd.fees', [
            'fees' => VisitFee::orderBy('doctor_id')->orderBy('visit_type')->get(),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'doctorNames' => Doctor::pluck('name', 'id'),
            'services' => PaymentCategory::orderBy('category')->get(['id', 'category', 'c_price']),
            'serviceNames' => PaymentCategory::pluck('category', 'id'),
        ]);
    }

    public function storeFee(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => 'nullable|integer|exists:doctor,id',
            'visit_type' => 'required|in:' . implode(',', array_keys(VisitFee::TYPES)),
            'shift' => 'required|in:' . implode(',', array_keys(VisitFee::SHIFTS)),
            'payment_category_id' => 'required|integer|exists:payment_category,id',
            'fee' => 'nullable|numeric|min:0',
        ]);
        $data['doctor_id'] = $data['doctor_id'] ?? null;
        $data['fee'] = isset($data['fee']) && $data['fee'] !== '' ? $data['fee'] : null;
        VisitFee::create($data + ['active' => true]);

        return redirect('/opd/visit-fees')->with('success', 'Visit fee added.');
    }

    public function destroyFee($id)
    {
        VisitFee::findOrFail($id)->delete();

        return redirect('/opd/visit-fees')->with('success', 'Visit fee removed.');
    }

    /* ---------------------------------------------------------------- booking slots */

    public function slots($doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);

        return view('app.opd.slots', [
            'doctor' => $doctor,
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'slots' => DoctorSlot::where('doctor_id', $doctor->id)->orderBy('weekday')->orderBy('start_time')->get(),
            'blocks' => SlotBlock::where('doctor_id', $doctor->id)->whereDate('date', '>=', now()->toDateString())->orderBy('date')->get(),
            'preview' => DoctorSchedule::freeSlots($doctor->id, now()->toDateString()),
        ]);
    }

    public function storeSlot(Request $request, $doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $data = $request->validate([
            'weekdays' => 'required|array|min:1', 'weekdays.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'required|integer|min:5|max:240',
        ]);

        foreach (array_unique($data['weekdays']) as $day) {
            DoctorSlot::create(['doctor_id' => $doctor->id, 'weekday' => $day, 'start_time' => $data['start_time'],
                'end_time' => $data['end_time'], 'slot_minutes' => $data['slot_minutes'], 'active' => true]);
        }

        return redirect('/doctors/' . $doctor->id . '/slots')->with('success', 'Slots created.');
    }

    public function toggleSlot($id)
    {
        $slot = DoctorSlot::findOrFail($id);
        $slot->update(['active' => ! $slot->active]);

        return back()->with('success', $slot->active ? 'Slots enabled.' : 'Slots disabled.');
    }

    public function destroySlot($id)
    {
        $slot = DoctorSlot::findOrFail($id);
        $slot->delete();

        return back()->with('success', 'Slots deleted.');
    }

    public function storeBlock(Request $request, $doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $data = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|required_with:start_time|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:120',
        ]);
        SlotBlock::create(['doctor_id' => $doctor->id, 'date' => $data['date'], 'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null, 'reason' => $data['reason'] ?? null]);

        return redirect('/doctors/' . $doctor->id . '/slots')->with('success', 'Time blocked.');
    }

    public function destroyBlock($id)
    {
        SlotBlock::findOrFail($id)->delete();

        return back()->with('success', 'Block removed.');
    }

    public function freeSlots(Request $request)
    {
        $request->validate(['doctor' => 'required|integer', 'date' => 'required|date']);

        return response()->json(DoctorSchedule::freeSlots((int) $request->input('doctor'), $request->input('date')));
    }
}
