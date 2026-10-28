<?php
/**
 * ICTHospital - fixes from the 28 October 2026 QA round.
 *
 * Pricing back to the list price, a printable invoice, tokens for visits marked arrived
 * from any screen, duplicate patients, no bookings in the past, and one visit fee per
 * doctor, visit type and shift.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\PaymentCategory;
use App\Models\ServicePrice;
use App\Models\User;
use App\Models\VisitFee;
use App\Support\Billing;
use App\Support\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QaFixesOct28Test extends TestCase
{
    use RefreshDatabase;

    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-26 14:23:00')); // a Monday afternoon

        $this->actingAs(User::create([
            'firstname' => 'Front', 'lastname' => 'Desk', 'login' => 'front', 'email' => 'f@example.test',
            'desc' => '', 'group' => 'Admin', 'password' => bcrypt('secret'),
        ]));
        $this->doctor = Doctor::create(['name' => 'Dr Laiba', 'img_url' => '', 'email' => '', 'address' => '', 'phone' => '',
            'department' => '', 'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function patient($name, $mr, array $extra = [])
    {
        return Patient::create(array_merge(['name' => $name, 'patient_id' => $mr, 'img_url' => '', 'email' => '', 'doctor' => '',
            'address' => '', 'phone' => '', 'sex' => '', 'age' => '', 'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test'], $extra));
    }

    // 1. Pricing back to the list price ------------------------------------------------

    public function test_reset_puts_a_types_prices_back_to_the_list_price(): void
    {
        $type = PatientType::create(['name' => 'Panel']);
        $ecg = PaymentCategory::create(['category' => 'ECG', 'c_price' => '1500']);
        $this->post('/pricing/adjust', ['type' => $type->id, 'percent' => 10, 'setting' => 'both'])->assertSessionHasNoErrors();
        $this->assertEquals(1650, (float) ServicePrice::where('patient_type_id', $type->id)->value('opd_price'));

        $this->post('/pricing/reset', ['type' => $type->id, 'setting' => 'both'])->assertSessionHasNoErrors();

        $this->assertSame(0, ServicePrice::where('patient_type_id', $type->id)->count());
        $this->assertEquals(1500, (float) \App\Support\PriceList::priceFor($ecg, $type->id, 'opd'));
    }

    public function test_reset_of_opd_only_keeps_the_ipd_prices(): void
    {
        $type = PatientType::create(['name' => 'Panel']);
        $ecg = PaymentCategory::create(['category' => 'ECG', 'c_price' => '1500']);
        ServicePrice::create(['payment_category_id' => $ecg->id, 'patient_type_id' => $type->id, 'opd_price' => 1650, 'ipd_price' => 1800]);

        $this->post('/pricing/reset', ['type' => $type->id, 'setting' => 'opd']);

        $row = ServicePrice::where('patient_type_id', $type->id)->first();
        $this->assertNull($row->opd_price);
        $this->assertEquals(1800, (float) $row->ipd_price);
    }

    // 2. Printable invoice ---------------------------------------------------------------

    public function test_the_invoice_prints_from_a_page_of_its_own(): void
    {
        $patient = $this->patient('Amina Yousaf', '000071');
        $service = PaymentCategory::create(['category' => 'Consultation, general', 'c_price' => '1000']);
        $invoice = Billing::raise($patient, $this->doctor->id, [['service' => $service, 'price' => 1000]], 'opd', 'test');

        $this->get('/invoices/' . $invoice->id)->assertOk()->assertSee('/invoices/' . $invoice->id . '/print', false);
        $this->get('/invoices/' . $invoice->id . '/print')->assertOk()
            ->assertSee('<td>#' . $invoice->id . '</td>', false)->assertSee('Consultation, general')
            ->assertSee('Amina Yousaf')->assertDontSee('sidebar-nav', false);
    }

    // 3. Tokens for visits marked arrived from any screen ---------------------------------

    public function test_an_appointment_saved_as_arrived_gets_a_token(): void
    {
        $patient = $this->patient('Ali Raza', '000072');
        $this->post('/appointments', ['patient' => $patient->id, 'doctor' => $this->doctor->id, 'date' => '2026-10-26',
            'start' => '14:30', 'minutes' => 15, 'status' => 'arrived'])->assertSessionHasNoErrors();

        $a = Appointment::firstOrFail();
        $this->assertSame(1, (int) $a->token_no);
        $this->assertNotNull($a->checked_in_at);
    }

    public function test_calling_in_a_visit_without_a_token_issues_one(): void
    {
        $patient = $this->patient('Sara Malik', '000073');
        $a = Appointment::create(['patient' => $patient->id, 'doctor' => $this->doctor->id, 'date' => '2026-10-26',
            'time_slot' => '14:30 - 14:45', 's_time' => '2026-10-26 14:30:00', 'e_time' => '2026-10-26 14:45:00',
            's_time_key' => '202610261430', 'status' => 'scheduled', 'remarks' => '', 'add_date' => '2026-10-26',
            'registration_time' => now()->toDateTimeString(), 'user' => '', 'request' => '']);
        \Illuminate\Support\Facades\DB::table('appointment')->where('id', $a->id)->update(['status' => 'arrived']); // as old data has it

        $this->get('/opd/queue')->assertOk()->assertSee('/opd/' . $a->id . '/check-in', false);
        $this->post('/opd/' . $a->id . '/call-in');

        $this->assertSame(1, (int) $a->fresh()->token_no);
        $this->assertSame('in consultation', $a->fresh()->status);
    }

    // 4. Duplicate patients ---------------------------------------------------------------

    public function test_a_cnic_already_on_file_is_refused(): void
    {
        $this->patient('Amina Yousaf', '000074', ['cnic' => '35202-1234567-1']);

        $this->post('/patients', ['name' => 'Amina Y', 'cnic' => '3520212345671'])->assertSessionHasErrors('cnic');
        $this->assertSame(1, Patient::count());
    }

    public function test_a_shared_phone_is_warned_about_and_can_be_accepted(): void
    {
        $this->patient('Bilal Ahmed', '000075', ['phone' => '03001234567']);

        $this->post('/patients', ['name' => 'Hina Bilal', 'phone' => '03001234567'])->assertSessionHasErrors('phone');
        $this->assertSame(1, Patient::count());

        $this->post('/patients', ['name' => 'Hina Bilal', 'phone' => '03001234567', 'allow_same_phone' => '1'])->assertSessionHasNoErrors();
        $this->assertSame(2, Patient::count());
    }

    // 5. No bookings in the past -----------------------------------------------------------

    public function test_free_slots_today_leave_out_times_that_have_passed(): void
    {
        DoctorSlot::create(['doctor_id' => $this->doctor->id, 'weekday' => 1, 'start_time' => '09:00', 'end_time' => '16:00', 'slot_minutes' => 15, 'active' => true]);

        $starts = array_column(DoctorSchedule::freeSlots($this->doctor->id, '2026-10-26'), 'start');

        $this->assertNotContains('09:00', $starts);
        $this->assertNotContains('14:15', $starts);
        $this->assertContains('14:30', $starts);
    }

    public function test_booking_a_time_that_has_passed_is_refused(): void
    {
        $patient = $this->patient('Usman Tariq', '000076');

        $this->post('/appointments', ['patient' => $patient->id, 'doctor' => $this->doctor->id, 'date' => '2026-10-26',
            'start' => '09:00', 'minutes' => 15, 'status' => 'scheduled'])->assertSessionHasErrors('start');
        $this->assertSame(0, Appointment::count());

        $this->post('/appointments', ['patient' => $patient->id, 'doctor' => $this->doctor->id, 'date' => '2026-10-27',
            'start' => '09:00', 'minutes' => 15, 'status' => 'scheduled'])->assertSessionHasNoErrors();
    }

    // 6. One visit fee per doctor, visit type and shift -------------------------------------

    public function test_a_second_fee_for_the_same_visits_is_refused_and_ties_go_to_the_first(): void
    {
        $opd = PaymentCategory::create(['category' => 'OPD consultation', 'c_price' => '1500']);
        $general = PaymentCategory::create(['category' => 'Consultation, general', 'c_price' => '1000']);
        $first = VisitFee::create(['doctor_id' => null, 'visit_type' => 'first', 'shift' => 'any', 'payment_category_id' => $opd->id, 'fee' => null, 'active' => true]);

        $this->post('/opd/visit-fees', ['visit_type' => 'first', 'shift' => 'any', 'payment_category_id' => $general->id, 'fee' => 1000])
            ->assertSessionHas('error');
        $this->assertSame(1, VisitFee::count());

        // A look-alike that slipped in before the rule: the first row added is billed, and the page says so.
        VisitFee::create(['doctor_id' => null, 'visit_type' => 'first', 'shift' => 'any', 'payment_category_id' => $general->id, 'fee' => 1000, 'active' => true]);
        $this->assertSame($first->id, VisitFee::resolve($this->doctor->id, 'first', now())->id);
        $this->get('/opd/visit-fees')->assertOk()->assertSee('Not used');
    }
}
