<?php
/**
 * ICTHospital - OPD tokens, waiting times, walk-ins, visit fees and booking slots.
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
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\ServicePrice;
use App\Models\SlotBlock;
use App\Models\User;
use App\Models\VisitFee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpdQueueTest extends TestCase
{
    use RefreshDatabase;

    private $doctor;
    private $p1;
    private $p2;
    private $consult;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-26 10:00:00')); // a Monday

        $this->actingAs(User::create([
            'firstname' => 'Front', 'lastname' => 'Desk', 'login' => 'front', 'email' => 'f@example.test',
            'desc' => '', 'group' => 'Admin', 'password' => bcrypt('secret'),
        ]));
        $this->doctor = Doctor::create(['name' => 'Dr Laiba', 'img_url' => '', 'email' => '', 'address' => '', 'phone' => '',
            'department' => '', 'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '']);
        $this->p1 = $this->patient('Ali Raza', '000041');
        $this->p2 = $this->patient('Sara Malik', '000042');
        $this->consult = PaymentCategory::create(['category' => 'OPD consultation', 'c_price' => '1500']);
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

    private function book($patient, $start = '10:00', $minutes = 15)
    {
        return $this->post('/appointments', ['patient' => $patient->id, 'doctor' => $this->doctor->id, 'date' => '2026-10-26',
            'start' => $start, 'minutes' => $minutes, 'status' => 'scheduled']);
    }

    public function test_check_in_hands_out_tokens_in_order_per_doctor_per_day(): void
    {
        $this->book($this->p1, '10:00');
        $this->book($this->p2, '10:15');
        [$a, $b] = Appointment::orderBy('id')->get()->all();

        $this->post('/opd/' . $b->id . '/check-in');
        $this->post('/opd/' . $a->id . '/check-in');
        $this->post('/opd/' . $a->id . '/check-in'); // twice keeps the token

        $this->assertSame(1, (int) $b->fresh()->token_no);
        $this->assertSame(2, (int) $a->fresh()->token_no);
        $this->assertSame('arrived', $a->fresh()->status);
    }

    public function test_waiting_and_treatment_times_come_from_the_timestamps(): void
    {
        $this->book($this->p1);
        $a = Appointment::first();
        $this->post('/opd/' . $a->id . '/check-in');
        Carbon::setTestNow(Carbon::parse('2026-10-26 10:20:00'));
        $this->post('/opd/' . $a->id . '/call-in');
        Carbon::setTestNow(Carbon::parse('2026-10-26 10:32:00'));
        $this->post('/opd/' . $a->id . '/done');

        $a = $a->fresh();
        $this->assertSame('completed', $a->status);
        $this->assertSame(20, (int) Carbon::parse($a->checked_in_at)->diffInMinutes(Carbon::parse($a->consult_started_at)));
        $this->assertSame(12, (int) Carbon::parse($a->consult_started_at)->diffInMinutes(Carbon::parse($a->checked_out_at)));
        $this->get('/opd/history?from=2026-10-26')->assertOk()->assertSee('20 / 12 min');
    }

    public function test_a_walk_in_gets_a_token_and_the_visit_fee_billed(): void
    {
        VisitFee::create(['visit_type' => 'first', 'shift' => 'any', 'payment_category_id' => $this->consult->id, 'fee' => 1200, 'active' => true]);

        $this->post('/opd/walk-in', ['patient' => $this->p1->id, 'doctor' => $this->doctor->id, 'visit_type' => 'first', 'bill' => 1])
            ->assertSessionHasNoErrors();

        $a = Appointment::first();
        $this->assertSame(1, (int) $a->token_no);
        $this->assertNotNull($a->payment_id);
        $this->assertSame('1200.00', (string) Payment::find($a->payment_id)->gross_total);
    }

    public function test_a_visit_is_billed_only_once(): void
    {
        VisitFee::create(['visit_type' => 'first', 'shift' => 'any', 'payment_category_id' => $this->consult->id, 'fee' => 1200, 'active' => true]);
        $this->book($this->p1);
        $a = Appointment::first();
        $this->post('/opd/' . $a->id . '/bill');
        $this->post('/opd/' . $a->id . '/bill');

        $this->assertSame(1, Payment::count());
    }

    public function test_the_doctors_own_fee_and_shift_win(): void
    {
        VisitFee::create(['visit_type' => 'follow_up', 'shift' => 'any', 'payment_category_id' => $this->consult->id, 'fee' => 800, 'active' => true]);
        VisitFee::create(['doctor_id' => $this->doctor->id, 'visit_type' => 'follow_up', 'shift' => 'morning', 'payment_category_id' => $this->consult->id, 'fee' => 1000, 'active' => true]);

        $this->assertSame('1000.00', (string) VisitFee::resolve($this->doctor->id, 'follow_up', Carbon::parse('2026-10-26 10:00'))->fee);
        $this->assertSame('800.00', (string) VisitFee::resolve($this->doctor->id, 'follow_up', Carbon::parse('2026-10-26 17:00'))->fee);
    }

    public function test_a_blank_visit_fee_uses_the_patient_type_price(): void
    {
        $panel = PatientType::create(['name' => 'Panel B', 'kind' => 'panel', 'active' => true]);
        ServicePrice::create(['payment_category_id' => $this->consult->id, 'patient_type_id' => $panel->id, 'opd_price' => 900]);
        VisitFee::create(['visit_type' => 'first', 'shift' => 'any', 'payment_category_id' => $this->consult->id, 'fee' => null, 'active' => true]);
        $p = $this->patient('Panel Patient', '000043', ['patient_type_id' => $panel->id]);

        $this->post('/opd/walk-in', ['patient' => $p->id, 'doctor' => $this->doctor->id, 'visit_type' => 'first', 'bill' => 1]);
        $this->assertSame('900.00', (string) Payment::first()->gross_total);
    }

    public function test_bookings_must_fit_the_doctors_slots_once_set(): void
    {
        $this->book($this->p1, '18:00')->assertSessionHasNoErrors(); // no slots yet: any time

        DoctorSlot::create(['doctor_id' => $this->doctor->id, 'weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00', 'slot_minutes' => 15, 'active' => true]);
        $this->book($this->p2, '14:00')->assertSessionHasErrors('start');
        $this->book($this->p2, '09:30')->assertSessionHasNoErrors();
    }

    public function test_a_blocked_day_cannot_be_booked(): void
    {
        SlotBlock::create(['doctor_id' => $this->doctor->id, 'date' => '2026-10-26', 'reason' => 'Conference']);
        $this->book($this->p1, '10:00')->assertSessionHasErrors('start');
    }

    public function test_free_slots_leave_out_bookings_and_blocks(): void
    {
        DoctorSlot::create(['doctor_id' => $this->doctor->id, 'weekday' => 1, 'start_time' => '09:00', 'end_time' => '10:00', 'slot_minutes' => 15, 'active' => true]);
        SlotBlock::create(['doctor_id' => $this->doctor->id, 'date' => '2026-10-26', 'start_time' => '09:45', 'end_time' => '10:00']);
        $this->book($this->p1, '09:15');

        $free = collect($this->getJson('/opd/free-slots?doctor=' . $this->doctor->id . '&date=2026-10-26')->json())->pluck('start')->all();
        $this->assertSame(['09:00', '09:30'], $free);
    }

    public function test_screens_render(): void
    {
        $this->book($this->p1);
        $a = Appointment::first();
        $this->post('/opd/' . $a->id . '/check-in');
        $this->get('/opd/queue?date=2026-10-26')->assertOk()->assertSee('Ali Raza')->assertSee('Waiting');
        $this->get('/opd/' . $a->id . '/token')->assertOk()->assertSee('Dr Laiba');
        $this->get('/opd/visit-fees')->assertOk();
        $this->get('/doctors/' . $this->doctor->id . '/slots')->assertOk()->assertSee('Create slots');
    }
}
