<?php
/**
 * ICTHospital - pharmacy dispensing.
 *
 * pharmacy_payment came across as a sale header with the items packed into
 * category_name and their prices into category_amount, matched by position.
 * Nothing decremented stock, so medicine.quantity only ever moved when somebody
 * edited it by hand and the figure on screen meant nothing.
 *
 * Dispensing now writes line items and takes the quantity out of the catalogue in
 * the same transaction, so the stock figure is a consequence of what was handed
 * over rather than an independent guess.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PharmacyPayment;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PharmacyController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $sales = PharmacyPayment::query()
            ->with('items')
            ->whereBetween('date', [$from, $to])
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('app.pharmacy.index', [
            'sales' => $sales,
            'from' => $from,
            'to' => $to,
            'patients' => Patient::whereIn('id', $sales->pluck('patient')->filter())->pluck('name', 'id'),
            'takings' => PharmacyPayment::whereBetween('date', [$from, $to])->get()
                ->sum(fn ($sale) => (float) $sale->gross_total),
        ]);
    }

    /**
     * Dispensing screen.
     *
     * A prescription can be passed in, which pre-loads its drugs. That is the
     * common case at a hospital pharmacy counter: somebody arrives holding one.
     */
    public function create(Request $request)
    {
        $prescription = $request->filled('prescription')
            ? Prescription::with('items')->find($request->input('prescription'))
            : null;

        return view('app.pharmacy.form', [
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
            'medicines' => Medicine::orderBy('name')->get(['id', 'name', 'generic', 'quantity', 's_price']),
            'prescription' => $prescription,
            'selectedPatient' => $prescription->patient ?? $request->input('patient'),
        ]);
    }

    public function store(Request $request)
    {
        $sale = DB::transaction(function () use ($request) {
            [$header, $lines] = $this->validated($request);

            $sale = PharmacyPayment::create($header);

            foreach ($lines as $line) {
                $sale->items()->create($line['row']);

                // Stock comes out inside the same transaction as the sale, so a
                // sale that fails cannot leave the catalogue short.
                if ($line['medicine']) {
                    $line['medicine']->decrement('quantity', $line['row']['quantity']);
                }
            }

            $this->refreshTotals($sale->fresh('items'));

            return $sale;
        });

        return redirect('/pharmacy/' . $sale->id)->with('success', 'Dispensed and receipted.');
    }

    public function show($id)
    {
        $sale = PharmacyPayment::with('items')->findOrFail($id);

        return view('app.pharmacy.show', [
            'sale' => $sale,
            'patient' => Patient::find($sale->patient),
        ]);
    }

    /**
     * Reversing a sale puts the stock back.
     *
     * Deleting without returning the quantity would leave the catalogue short by
     * however much the cancelled sale took, which is how stock figures become
     * fiction.
     */
    public function destroy($id)
    {
        $sale = PharmacyPayment::with('items')->findOrFail($id);

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                if ($item->medicine_id && $medicine = Medicine::find($item->medicine_id)) {
                    $medicine->increment('quantity', $item->quantity);
                }
            }

            $sale->items()->delete();
            $sale->delete();
        });

        return redirect('/pharmacy')->with('success', 'Sale reversed and stock returned.');
    }

    private function validated(Request $request)
    {
        $input = $request->validate([
            'patient' => 'nullable|integer|exists:patient,id',
            'date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'amount_received' => 'nullable|numeric|min:0',
            'items' => 'array',
            'items.*.medicine_id' => 'nullable|integer|exists:medicine,id',
            'items.*.name' => 'nullable|string|max:200',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $lines = $this->buildLines($input['items'] ?? []);

        if (empty($lines)) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one medicine to dispense.',
            ]);
        }

        $subtotal = array_sum(array_column(array_column($lines, 'row'), 'line_total'));
        $discount = (float) ($input['discount'] ?? 0);
        $vat = (float) ($input['vat'] ?? 0);
        $gross = max(0, $subtotal - $discount + $vat);

        return [[
            'patient' => $input['patient'] ?? null,
            'doctor' => null,
            'date' => $input['date'],
            'amount' => number_format($subtotal, 2, '.', ''),
            'discount' => number_format($discount, 2, '.', ''),
            'vat' => number_format($vat, 2, '.', ''),
            'gross_total' => number_format($gross, 2, '.', ''),
            'amount_received' => number_format((float) ($input['amount_received'] ?? $gross), 2, '.', ''),
            'status' => 'paid',
            'category' => null,
            'category_name' => null,
            'category_amount' => null,
            'x_ray' => null,
            'flat_vat' => null,
            'flat_discount' => null,
            'hospital_amount' => null,
            'doctor_amount' => null,
        ], $lines];
    }

    /**
     * Turns the submitted rows into line items, checking stock as it goes.
     *
     * Stock is checked here and taken in the transaction. The window between the
     * two is small but real, which is why the decrement is a database level
     * operation rather than a read, subtract and write.
     */
    private function buildLines(array $rows)
    {
        $lines = [];
        $wanted = [];

        foreach ($rows as $index => $row) {
            $id = $row['medicine_id'] ?? null;
            $quantity = (int) ($row['quantity'] ?? 0);
            $name = trim((string) ($row['name'] ?? ''));
            $medicine = $id ? Medicine::find($id) : null;

            if ($medicine) {
                $name = $medicine->name;
            }

            if ($name === '' || $quantity < 1) {
                continue;
            }

            if ($medicine) {
                // A medicine listed twice on one sale has to be counted together,
                // or each line passes the stock check on its own and the total
                // still goes over.
                $wanted[$medicine->id] = ($wanted[$medicine->id] ?? 0) + $quantity;

                if ($wanted[$medicine->id] > (int) $medicine->quantity) {
                    throw ValidationException::withMessages([
                        'items' => sprintf(
                            'Only %d of %s left in stock, and the sale asks for %d.',
                            (int) $medicine->quantity,
                            $medicine->name,
                            $wanted[$medicine->id]
                        ),
                    ]);
                }
            }

            // validate() omits absent nullable keys, so this must not index the
            // array directly. Blank means "use the catalogue price".
            $given = $row['unit_price'] ?? null;
            $unit = ($given !== null && $given !== '')
                ? (float) $given
                : (float) ($medicine->s_price ?? 0);

            $lines[] = [
                'medicine' => $medicine,
                'row' => [
                    'medicine_id' => $medicine->id ?? null,
                    'name' => $name,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unit, 2, '.', ''),
                    'line_total' => number_format($unit * $quantity, 2, '.', ''),
                ],
            ];
        }

        return $lines;
    }

    /** Keeps the legacy packed columns readable for anything still using them. */
    private function refreshTotals(PharmacyPayment $sale)
    {
        $sale->update([
            'category_name' => substr($sale->items->map->summary()->implode(', '), 0, 1000),
            'category_amount' => substr($sale->items->pluck('line_total')->implode(', '), 0, 1000),
        ]);
    }
}
