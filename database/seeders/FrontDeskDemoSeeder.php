<?php
/**
 * ICTHospital - demonstration data for the front desk and OPD features:
 * patient types and panel prices, doctor share, visit fees and slots, today's
 * token queue with a consultation, radiology, and a closed shift.
 *
 * Called from DemoDataSeeder. Safe to run twice: it skips what is already there.
 * Never run on a live system.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\PaymentCategory;
use App\Models\RadiologyExam;
use App\Models\RadiologyOrder;
use App\Models\ServicePrice;
use App\Models\SharePolicy;
use App\Models\ShareRule;
use App\Models\VisitFee;
use App\Support\Billing;
use Illuminate\Database\Seeder;

class FrontDeskDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (SharePolicy::where('name', 'Standard OPD 70%')->exists()) {
            $this->command && $this->command->line('Front desk demo data already present.');

            return;
        }

        $doctors = Doctor::orderBy('id')->get();
        $patients = Patient::orderBy('id')->get();
        if ($doctors->isEmpty() || $patients->count() < 3) {
            return;
        }

        // Patient types and a panel price list.
        $panel = PatientType::firstOrCreate(['name' => 'Jubilee Life panel'], ['kind' => 'panel', 'company' => 'Jubilee Life', 'active' => true]);
        PatientType::firstOrCreate(['name' => 'Charity'], ['kind' => 'charity', 'active' => true]);
        PatientType::firstOrCreate(['name' => 'Online booking'], ['kind' => 'online', 'active' => true]);
        $patients[1]->update(['patient_type_id' => $panel->id, 'cnic' => '35202-1234567-1', 'city' => 'Lahore']);

        $consult = PaymentCategory::firstOrCreate(['category' => 'OPD consultation'], ['c_price' => '1500.00', 'department' => 'General OPD', 'service_kind' => 'consultation']);
        $follow = PaymentCategory::firstOrCreate(['category' => 'OPD follow-up'], ['c_price' => '800.00', 'department' => 'General OPD', 'service_kind' => 'consultation']);
        $us = PaymentCategory::firstOrCreate(['category' => 'Ultrasound abdomen'], ['c_price' => '2500.00', 'department' => 'Radiology', 'service_kind' => 'radiology']);
        $xray = PaymentCategory::firstOrCreate(['category' => 'X-ray chest PA'], ['c_price' => '1200.00', 'department' => 'Radiology', 'service_kind' => 'radiology']);
        PaymentCategory::firstOrCreate(['category' => 'Dialysis session'], ['c_price' => '7000.00', 'department' => 'Nephrology', 'service_kind' => 'dialysis']);
        foreach ([[$consult, 1800, 2200], [$us, 3000, 3500], [$xray, 1500, 1800]] as [$svc, $opd, $ipd]) {
            ServicePrice::updateOrCreate(['payment_category_id' => $svc->id, 'patient_type_id' => $panel->id], ['opd_price' => $opd, 'ipd_price' => $ipd]);
        }

        // Doctor share: 70% of OPD, 50% of everything else, 100% for the panel; 10% to a referring doctor.
        $policy = SharePolicy::create(['name' => 'Standard OPD 70%', 'notes' => 'Demo policy', 'active' => true]);
        ShareRule::create(['share_policy_id' => $policy->id, 'doctor_percent' => 50, 'referral_percent' => 10]);
        ShareRule::create(['share_policy_id' => $policy->id, 'department' => 'General OPD', 'doctor_percent' => 70, 'referral_percent' => 0]);
        ShareRule::create(['share_policy_id' => $policy->id, 'department' => 'General OPD', 'patient_type_id' => $panel->id, 'doctor_percent' => 100]);
        Doctor::whereIn('id', $doctors->pluck('id'))->update(['share_policy_id' => $policy->id]);

        // Visit fees and clinic slots for the first doctor, Monday to Saturday.
        VisitFee::create(['visit_type' => 'first', 'shift' => 'any', 'payment_category_id' => $consult->id, 'fee' => null, 'active' => true]);
        VisitFee::create(['visit_type' => 'follow_up', 'shift' => 'any', 'payment_category_id' => $follow->id, 'fee' => null, 'active' => true]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            DoctorSlot::create(['doctor_id' => $doctors[0]->id, 'weekday' => $day, 'start_time' => '09:00', 'end_time' => '14:00', 'slot_minutes' => 15, 'active' => true]);
        }

        // Radiology catalogue.
        $usExam = RadiologyExam::create(['name' => 'Ultrasound abdomen', 'modality' => 'ultrasound', 'payment_category_id' => $us->id,
            'report_template' => "Liver:\nGall bladder:\nPancreas:\nKidneys:\nBladder:", 'active' => true]);
        RadiologyExam::create(['name' => 'X-ray chest PA', 'modality' => 'xray', 'payment_category_id' => $xray->id, 'active' => true]);

        // Today's queue: one done with a consultation, one with the doctor, two waiting.
        $doctor = $doctors[0];
        $base = now()->setTime(9, 0);
        $states = ['completed', 'in consultation', 'arrived', 'arrived'];
        foreach ($patients->take(4)->values() as $i => $p) {
            $start = $base->copy()->addMinutes(15 * $i);
            $checkIn = $start->copy()->subMinutes(5);
            $a = Appointment::create([
                'patient' => $p->id, 'doctor' => $doctor->id, 'date' => $start->toDateString(),
                'time_slot' => $start->format('H:i') . ' - ' . $start->copy()->addMinutes(15)->format('H:i'),
                's_time' => $start->toDateTimeString(), 'e_time' => $start->copy()->addMinutes(15)->toDateTimeString(),
                's_time_key' => $start->format('YmdHi'), 'status' => $states[$i], 'remarks' => '', 'add_date' => $start->toDateString(),
                'registration_time' => now()->toDateTimeString(), 'user' => '', 'request' => '', 'visit_type' => 'first',
                'token_no' => $i + 1, 'checked_in_at' => $checkIn,
                'consult_started_at' => $i < 2 ? $checkIn->copy()->addMinutes(12 + 3 * $i) : null,
                'checked_out_at' => $i === 0 ? $checkIn->copy()->addMinutes(24) : null,
                'b_p' => $i < 2 ? '128/84' : null, 'pulse' => $i < 2 ? '82' : null,
                'mode' => $i === 3 ? 'video' : 'in_person', 'video_room' => $i === 3 ? \App\Support\Telemedicine::newRoom() : null,
            ]);
            if ($i < 3) {
                $invoice = Billing::raise($p->fresh(), $doctor->id, [['service' => $consult]], 'opd', 'OPD visit, token ' . ($i + 1));
                $a->update(['payment_id' => $invoice->id]);
                if ($i < 2) {
                    $invoice->payments()->create(['amount' => $invoice->gross_total, 'method' => $i === 0 ? 'Cash' : 'Card',
                        'paid_at' => now()->toDateTimeString(), 'received_by' => \App\Models\User::where('group', 'Admin')->value('id')]);
                    $invoice->update(['amount_received' => $invoice->gross_total, 'status' => 'paid']);
                }
            }
            if ($i === 0) {
                Consultation::create([
                    'appointment_id' => $a->id, 'patient_id' => $p->id, 'doctor_id' => $doctor->id, 'date' => $a->date,
                    'presenting_complaint' => 'Upper abdominal pain after meals for two weeks.',
                    'history' => 'Pain worse with fatty food, no vomiting, no weight loss.',
                    'examination' => ['abdomen' => ['status' => 'abnormal', 'note' => 'Tender right upper quadrant'], 'cvs' => ['status' => 'normal']],
                    'provisional_diagnosis' => 'Biliary colic, query gallstones', 'plan' => 'Ultrasound abdomen, LFTs.',
                    'treatment' => 'Low fat diet. Analgesia as needed.', 'follow_up_date' => now()->addDays(7)->toDateString(),
                    'follow_up_notes' => 'Review with reports', 'status' => 'completed', 'completed_at' => now(),
                ]);
                $order = RadiologyOrder::place($p, $doctor->id, [$usExam->id], false, 'Biliary colic, query gallstones', $a->id)[0];
                $order->update(['status' => 'reported', 'performed_at' => now(), 'reported_at' => now(),
                    'findings' => "Liver: normal size and echotexture.\nGall bladder: multiple calculi, largest 9 mm, wall not thickened.\nKidneys: normal.",
                    'impression' => 'Cholelithiasis without cholecystitis.']);
            }
        }

        $this->command && $this->command->info('Front desk demo data: patient types, panel prices, doctor share, visit fees, slots, radiology and today\'s queue.');
    }
}
