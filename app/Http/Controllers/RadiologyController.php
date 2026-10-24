<?php
/**
 * ICTHospital - radiology: worklist, ordering, reporting, billing and the exam catalogue.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PaymentCategory;
use App\Models\RadiologyExam;
use App\Models\RadiologyOrder;
use App\Models\User;
use App\Support\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RadiologyController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** The radiologist's worklist: urgent first, then oldest first. */
    public function index(Request $request)
    {
        $status = (string) $request->input('status', 'open');
        $modality = (string) $request->input('modality', '');
        $from = (string) $request->input('from', '');
        $to = (string) $request->input('to', '');

        $orders = RadiologyOrder::query()
            ->when($status === 'open', fn ($q) => $q->whereIn('status', ['ordered', 'scheduled', 'done']))
            ->when($status !== 'open' && $status !== '', fn ($q) => $q->where('status', $status))
            ->when($modality !== '', fn ($q) => $q->where('modality', $modality))
            ->when($from !== '', fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to !== '', fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->orderByDesc('urgent')->orderBy('created_at')->limit(300)->get();

        return view('app.radiology.index', [
            'orders' => $orders,
            'patients' => Patient::whereIn('id', $orders->pluck('patient_id'))->get()->keyBy('id'),
            'doctorNames' => Doctor::pluck('name', 'id'),
            'filters' => compact('status', 'modality', 'from', 'to'),
        ]);
    }

    public function create(Request $request)
    {
        return view('app.radiology.create', [
            'patientId' => $request->input('patient'),
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'exams' => RadiologyExam::where('active', true)->orderBy('modality')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient' => 'required|integer|exists:patient,id',
            'doctor' => 'nullable|integer|exists:doctor,id',
            'exams' => 'required|array|min:1', 'exams.*' => 'integer|exists:radiology_exam,id',
            'urgent' => 'nullable|boolean',
            'clinical_notes' => 'nullable|string|max:1000',
            'bill' => 'nullable|boolean',
        ], ['exams.required' => 'Choose at least one exam.']);

        $patient = Patient::findOrFail($data['patient']);
        $orders = DB::transaction(function () use ($patient, $data) {
            $orders = RadiologyOrder::place($patient, $data['doctor'] ?? null, $data['exams'], ! empty($data['urgent']), $data['clinical_notes'] ?? null);
            if (! empty($data['bill'])) {
                $this->billOrders($patient, collect($orders));
            }

            return $orders;
        });

        return redirect('/radiology')->with('success', count($orders) . ' exam(s) ordered for ' . $patient->name . '.');
    }

    public function show($id)
    {
        $order = RadiologyOrder::findOrFail($id);

        return view('app.radiology.show', [
            'order' => $order,
            'patient' => Patient::find($order->patient_id),
            'doctor' => Doctor::find($order->doctor_id),
            'reporter' => $order->reported_by ? User::find($order->reported_by) : null,
            'history' => RadiologyOrder::where('patient_id', $order->patient_id)->where('id', '!=', $order->id)->orderByDesc('created_at')->limit(10)->get(),
        ]);
    }

    /** Status moves and the report. A reported exam can still be corrected; it is stamped again. */
    public function update(Request $request, $id)
    {
        $order = RadiologyOrder::findOrFail($id);
        $data = $request->validate([
            'action' => 'required|in:schedule,done,report,cancel',
            'scheduled_at' => 'nullable|date',
            'findings' => 'nullable|string|max:20000',
            'impression' => 'nullable|string|max:5000',
        ]);

        if ($order->status === 'cancelled') {
            throw ValidationException::withMessages(['action' => 'This order was cancelled.']);
        }

        switch ($data['action']) {
            case 'schedule':
                $order->update(['status' => 'scheduled', 'scheduled_at' => $data['scheduled_at'] ?? now()]);
                break;
            case 'done':
                $order->update(['status' => $order->status === 'reported' ? 'reported' : 'done', 'performed_at' => $order->performed_at ?: now()]);
                break;
            case 'report':
                if (trim((string) ($data['impression'] ?? '')) === '') {
                    throw ValidationException::withMessages(['impression' => 'A report needs an impression.']);
                }
                $order->update(['status' => 'reported', 'findings' => $data['findings'] ?? null, 'impression' => $data['impression'],
                    'performed_at' => $order->performed_at ?: now(), 'reported_by' => auth()->id(), 'reported_at' => now()]);
                break;
            case 'cancel':
                if ($order->status === 'reported') {
                    throw ValidationException::withMessages(['action' => 'A reported exam cannot be cancelled.']);
                }
                $order->update(['status' => 'cancelled']);
                break;
        }

        return redirect('/radiology/' . $order->id)->with('success', 'Saved.');
    }

    public function bill($id)
    {
        $order = RadiologyOrder::findOrFail($id);
        if ($order->payment_id) {
            return back()->with('error', 'Already billed on invoice #' . $order->payment_id . '.');
        }
        $invoice = DB::transaction(fn () => $this->billOrders(Patient::findOrFail($order->patient_id), collect([$order])));

        return $invoice
            ? back()->with('success', 'Billed on invoice #' . $invoice->id . '.')
            : back()->with('error', 'This exam has no service on the price list. Link one under Radiology, Exam catalogue.');
    }

    public function printout($id)
    {
        $order = RadiologyOrder::findOrFail($id);

        return view('app.radiology.print', [
            'order' => $order, 'patient' => Patient::find($order->patient_id), 'doctor' => Doctor::find($order->doctor_id),
            'reporter' => $order->reported_by ? User::find($order->reported_by) : null,
        ]);
    }

    /** One invoice for the orders that have a billable service. */
    private function billOrders(Patient $patient, $orders)
    {
        $exams = RadiologyExam::whereIn('id', $orders->pluck('radiology_exam_id'))->get()->keyBy('id');
        $lines = [];
        $billed = [];
        foreach ($orders as $o) {
            $exam = $exams->get($o->radiology_exam_id);
            $service = $exam && $exam->payment_category_id ? PaymentCategory::find($exam->payment_category_id) : null;
            if ($service && ! $o->payment_id) {
                $lines[] = ['service' => $service, 'name' => $o->name];
                $billed[] = $o;
            }
        }
        if (! $lines) {
            return null;
        }

        $invoice = Billing::raise($patient, $orders->first()->doctor_id, $lines, 'opd', 'Radiology');
        foreach ($billed as $o) {
            $o->update(['payment_id' => $invoice->id]);
        }

        return $invoice;
    }

    /* ---------------------------------------------------------------- catalogue */

    public function catalogue()
    {
        return view('app.radiology.catalogue', [
            'exams' => RadiologyExam::orderBy('modality')->orderBy('name')->get(),
            'services' => PaymentCategory::orderBy('category')->get(['id', 'category', 'c_price']),
        ]);
    }

    public function storeExam(Request $request)
    {
        RadiologyExam::create($this->validatedExam($request) + ['active' => true]);

        return redirect('/radiology/catalogue')->with('success', 'Exam added.');
    }

    public function updateExam(Request $request, $id)
    {
        $exam = RadiologyExam::findOrFail($id);
        $exam->update($this->validatedExam($request) + ['active' => $request->boolean('active')]);

        return redirect('/radiology/catalogue')->with('success', 'Saved ' . $exam->name . '.');
    }

    private function validatedExam(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'modality' => 'required|in:' . implode(',', array_keys(RadiologyExam::MODALITIES)),
            'payment_category_id' => 'nullable|integer|exists:payment_category,id',
            'report_template' => 'nullable|string|max:10000',
        ]);

        return ['name' => $data['name'], 'modality' => $data['modality'], 'payment_category_id' => $data['payment_category_id'] ?? null,
            'report_template' => $data['report_template'] ?? null];
    }
}
