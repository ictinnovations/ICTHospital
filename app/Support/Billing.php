<?php
/**
 * ICTHospital - raises an invoice from code (an OPD visit fee, a radiology order).
 *
 * Writes the same header, lines, legacy summary columns and doctor shares as an
 * invoice raised on the invoice screen, priced from the patient's type.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\Payment;
use App\Models\PaymentCategory;

class Billing
{
    /**
     * @param array $lines each ['service' => PaymentCategory, 'price' => float|null, 'quantity' => int]
     */
    public static function raise(Patient $patient, $doctorId, array $lines, $setting = 'opd', $remarks = null)
    {
        $typeId = $patient->patient_type_id ?: PatientType::defaultId();
        $items = [];
        foreach ($lines as $l) {
            /** @var PaymentCategory $service */
            $service = $l['service'];
            $qty = max(1, (int) ($l['quantity'] ?? 1));
            $unit = isset($l['price']) && $l['price'] !== null ? round((float) $l['price'], 2) : PriceList::priceFor($service, $typeId, $setting);
            $items[] = [
                'payment_category_id' => $service->id, 'name' => $l['name'] ?? $service->category, 'quantity' => $qty,
                'unit_price' => number_format($unit, 2, '.', ''), 'line_total' => number_format($unit * $qty, 2, '.', ''),
            ];
        }

        $subtotal = array_sum(array_map(fn ($i) => (float) $i['line_total'], $items));
        $doctor = $doctorId ? Doctor::find($doctorId) : null;

        $invoice = Payment::create([
            'patient' => $patient->id, 'doctor' => $doctorId, 'date' => now()->toDateString(), 'date_string' => now()->toDateString(),
            'amount' => number_format($subtotal, 2, '.', ''), 'discount' => '0.00', 'vat' => '0.00',
            'gross_total' => number_format($subtotal, 2, '.', ''), 'amount_received' => '0.00', 'remarks' => $remarks, 'status' => 'unpaid',
            'user' => (string) (auth()->id() ?? ''), 'patient_name' => $patient->name, 'patient_phone' => $patient->phone,
            'patient_address' => $patient->address, 'doctor_name' => $doctor->name ?? null, 'patient_type_id' => $typeId, 'care_setting' => $setting,
        ]);

        foreach ($items as $item) {
            $invoice->items()->create($item);
        }

        $invoice = $invoice->fresh('items');
        $invoice->update([
            'category_name' => substr($invoice->items->map->summary()->implode(', '), 0, 1000),
            'category_amount' => substr($invoice->items->pluck('line_total')->implode(', '), 0, 1000),
            'category' => $invoice->items->pluck('payment_category_id')->filter()->implode(','),
        ]);
        ShareCalculator::forInvoice($invoice);

        return $invoice;
    }
}
