<?php
/**
 * ICTHospital - works out doctor shares for an invoice.
 *
 * The base of a share is what the hospital actually charged for the line: the line
 * total less its part of any invoice discount, spread over the lines by value. Tax
 * is not shared. The performing doctor is the line's doctor, or the invoice's doctor
 * when the line has none; the referring doctor is the one recorded on the invoice.
 *
 * Shares already approved or paid are never recalculated, so a later change to a
 * policy cannot alter money that has been signed off.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use App\Models\Doctor;
use App\Models\DoctorShare;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\ShareRule;

class ShareCalculator
{
    public static function forInvoice(Payment $invoice)
    {
        $invoice->loadMissing('items');

        if (DoctorShare::where('payment_id', $invoice->id)->where('status', '!=', 'pending')->exists()) {
            return;
        }
        DoctorShare::where('payment_id', $invoice->id)->delete();

        $subtotal = (float) $invoice->items->sum('line_total');
        $discount = min((float) $invoice->discount, $subtotal);
        $services = PaymentCategory::whereIn('id', $invoice->items->pluck('payment_category_id')->filter())->get()->keyBy('id');

        foreach ($invoice->items as $item) {
            $line = (float) $item->line_total;
            $base = $subtotal > 0 ? round($line - $discount * ($line / $subtotal), 2) : 0.0;
            if ($base <= 0) {
                continue;
            }

            $service = $item->payment_category_id ? $services->get($item->payment_category_id) : null;
            $department = $service->department ?? null;

            $performer = $item->doctor_id ?: $invoice->doctor;
            if ($performer) {
                self::write($invoice, $item, (int) $performer, 'performing', $base, $item->payment_category_id, $department);
            }

            if ($invoice->referring_doctor && (int) $invoice->referring_doctor !== (int) $performer) {
                self::write($invoice, $item, (int) $invoice->referring_doctor, 'referring', $base, $item->payment_category_id, $department);
            }
        }
    }

    /** The best matching rule of the doctor's policy, or null. */
    public static function ruleFor($doctorId, $serviceId, $department, $patientTypeId)
    {
        $policyId = Doctor::whereKey($doctorId)->value('share_policy_id');
        if (! $policyId) {
            return null;
        }

        $best = null;
        $bestScore = -1;
        foreach (ShareRule::where('share_policy_id', $policyId)->orderBy('id')->get() as $rule) {
            $score = $rule->score($serviceId, $department, $patientTypeId);
            if ($score > $bestScore) {
                $best = $rule;
                $bestScore = $score;
            }
        }

        return $best;
    }

    private static function write(Payment $invoice, $item, $doctorId, $role, $base, $serviceId, $department)
    {
        $rule = self::ruleFor($doctorId, $serviceId, $department, $invoice->patient_type_id);
        if (! $rule) {
            return;
        }

        $percent = (float) ($role === 'performing' ? $rule->doctor_percent : $rule->referral_percent);
        if ($percent <= 0) {
            return;
        }

        DoctorShare::create([
            'payment_id' => $invoice->id,
            'invoice_item_id' => $item->id,
            'doctor_id' => $doctorId,
            'role' => $role,
            'share_rule_id' => $rule->id,
            'base_amount' => $base,
            'percent' => $percent,
            'amount' => round($base * $percent / 100, 2),
            'status' => 'pending',
        ]);
    }
}
