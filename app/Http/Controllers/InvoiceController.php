<?php
/**
 * ICTHospital - patient invoicing.
 *
 * The last of the packed string pairs. payment.category_name held every charge
 * and payment.category_amount held every price, matched by position, so a
 * mismatched pair silently moved every amount onto the wrong service and nothing
 * could be totalled without parsing two strings and trusting they lined up.
 *
 * Charges are rows in invoice_item. Payments are rows in invoice_payment, because
 * the legacy single amount_received column cannot express a deposit now and the
 * balance later, which is the ordinary case.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $show = $request->input('show', 'outstanding');
        $patientId = $request->input('patient', '');

        $invoices = Payment::query()
            ->with(['items', 'payments'])
            ->when($patientId !== '', fn ($q) => $q->where('patient', $patientId))
            ->orderByDesc('id')
            ->get();

        // Settlement is derived from the payment rows, so the filter is applied
        // after loading rather than as a where clause on a status column.
        if ($show === 'outstanding') {
            $invoices = $invoices->filter(fn ($i) => $i->balance() > 0.004);
        } elseif ($show === 'settled') {
            $invoices = $invoices->filter(fn ($i) => $i->balance() <= 0.004);
        }

        return view('app.invoices.index', [
            'invoices' => $invoices->take(200),
            'show' => $show,
            'patientId' => $patientId,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'owed' => $invoices->sum(fn ($i) => max(0, $i->balance())),
        ]);
    }

    public function create(Request $request)
    {
        return view('app.invoices.form', [
            'invoice' => new Payment(['date' => now()->toDateString(), 'patient' => $request->input('patient')]),
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'doctors' => Doctor::orderBy('name')->get(['id', 'name']),
            'services' => PaymentCategory::orderBy('category')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $invoice = DB::transaction(function () use ($request) {
            [$header, $items] = $this->validated($request);
            $invoice = Payment::create($header);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            $this->refreshTotals($invoice->fresh('items'));

            return $invoice;
        });

        return redirect('/invoices/' . $invoice->id)->with('success', 'Invoice raised.');
    }

    public function show($id)
    {
        $invoice = Payment::with(['items', 'payments'])->findOrFail($id);

        return view('app.invoices.show', [
            'invoice' => $invoice,
            'patient' => Patient::find($invoice->patient),
            'doctor' => Doctor::find($invoice->doctor),
        ]);
    }

    public function destroy($id)
    {
        $invoice = Payment::findOrFail($id);

        if ($invoice->paid() > 0) {
            return redirect('/invoices/' . $invoice->id)
                ->with('error', 'Money has been taken against this invoice, so it cannot be deleted.');
        }

        DB::transaction(function () use ($invoice) {
            $invoice->items()->delete();
            $invoice->delete();
        });

        return redirect('/invoices')->with('success', 'Invoice removed.');
    }

    /**
     * Takes a payment.
     *
     * Overpayment is refused rather than accepted and left as a negative balance
     * for somebody to puzzle over later.
     */
    public function pay(Request $request, $id)
    {
        $invoice = Payment::with('payments')->findOrFail($id);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|string|max:60',
            'reference' => 'nullable|string|max:120',
        ]);

        $amount = round((float) $data['amount'], 2);

        // The balance check and the insert have to be one atomic step. Checking
        // first and writing afterwards let two part payments both see the same
        // outstanding figure and both be accepted, overpaying the invoice.
        DB::transaction(function () use ($invoice, $data, $amount) {
            $invoice->newQuery()->whereKey($invoice->id)->lockForUpdate()->first();
            $current = $invoice->fresh('payments');

            if ($amount > $current->balance() + 0.004) {
                throw ValidationException::withMessages([
                    'amount' => sprintf(
                        'Only %s is outstanding on this invoice.',
                        number_format($current->balance(), 2)
                    ),
                ]);
            }

            $invoice->payments()->create([
                'amount' => number_format($amount, 2, '.', ''),
                'method' => $data['method'] ?? null,
                'reference' => $data['reference'] ?? null,
                'paid_at' => now()->toDateTimeString(),
            ]);

            // The legacy columns are kept in step so old reports still read.
            $fresh = $invoice->fresh('payments');
            $invoice->update([
                'amount_received' => number_format($fresh->paid(), 2, '.', ''),
                'status' => $fresh->settlement(),
            ]);
        });

        return redirect('/invoices/' . $invoice->id)->with('success', 'Payment recorded.');
    }

    private function validated(Request $request)
    {
        $input = $request->validate([
            'patient' => 'required|integer|exists:patient,id',
            'doctor' => 'nullable|integer|exists:doctor,id',
            'date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
            'items' => 'array',
            'items.*.payment_category_id' => 'nullable|integer|exists:payment_category,id',
            'items.*.name' => 'nullable|string|max:200',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $items = $this->buildItems($input['items'] ?? []);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one charge, or the invoice is for nothing.',
            ]);
        }

        $subtotal = array_sum(array_map(fn ($i) => (float) $i['line_total'], $items));
        $discount = (float) ($input['discount'] ?? 0);
        $vat = (float) ($input['vat'] ?? 0);

        if ($discount > $subtotal) {
            throw ValidationException::withMessages([
                'discount' => 'The discount is larger than the invoice.',
            ]);
        }

        $gross = round($subtotal - $discount + $vat, 2);
        $patient = Patient::find($input['patient']);
        $doctor = ! empty($input['doctor']) ? Doctor::find($input['doctor']) : null;

        return [[
            'patient' => $input['patient'],
            'doctor' => $input['doctor'] ?? null,
            'date' => $input['date'],
            'date_string' => $input['date'],
            'amount' => number_format($subtotal, 2, '.', ''),
            'discount' => number_format($discount, 2, '.', ''),
            'vat' => number_format($vat, 2, '.', ''),
            'gross_total' => number_format($gross, 2, '.', ''),
            'amount_received' => '0.00',
            'remarks' => $input['remarks'] ?? null,
            'status' => 'unpaid',
            'user' => (string) (auth()->id() ?? ''),
            'patient_name' => $patient->name ?? null,
            'patient_phone' => $patient->phone ?? null,
            'patient_address' => $patient->address ?? null,
            'doctor_name' => $doctor->name ?? null,
            'category' => null,
            'category_name' => null,
            'category_amount' => null,
            'x_ray' => null,
            'flat_vat' => null,
            'flat_discount' => null,
            'hospital_amount' => null,
            'doctor_amount' => null,
            'deposit_type' => null,
        ], $items];
    }

    private function buildItems(array $rows)
    {
        $services = PaymentCategory::all()->keyBy('id');
        $items = [];

        foreach ($rows as $row) {
            $id = $row['payment_category_id'] ?? null;
            $service = $id ? $services->get($id) : null;
            $name = trim((string) ($row['name'] ?? ''));
            $quantity = (int) ($row['quantity'] ?? 1);

            if ($service) {
                $name = $service->category;
            }

            if ($name === '' || $quantity < 1) {
                continue;
            }

            $given = $row['unit_price'] ?? null;
            $unit = ($given !== null && $given !== '')
                ? (float) $given
                : (float) ($service->c_price ?? 0);

            $items[] = [
                'payment_category_id' => $service->id ?? null,
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => number_format($unit, 2, '.', ''),
                'line_total' => number_format($unit * $quantity, 2, '.', ''),
            ];
        }

        return $items;
    }

    private function refreshTotals(Payment $invoice)
    {
        $invoice->update([
            'category_name' => substr($invoice->items->map->summary()->implode(', '), 0, 1000),
            'category_amount' => substr($invoice->items->pluck('line_total')->implode(', '), 0, 1000),
            'category' => $invoice->items->pluck('payment_category_id')->filter()->implode(','),
        ]);
    }
}
