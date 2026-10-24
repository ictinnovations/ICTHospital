<?php
/**
 * ICTHospital - radiology orders, reporting and billing.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\RadiologyExam;
use App\Models\RadiologyOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RadiologyTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $doctor;
    private $us;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::create(['firstname' => 'Rad', 'lastname' => 'User', 'login' => 'rad', 'email' => 'rad@example.test',
            'desc' => '', 'group' => 'Admin', 'password' => bcrypt('secret')]));
        $this->patient = Patient::create(['name' => 'Asia Abbas', 'patient_id' => '000060', 'img_url' => '', 'email' => '', 'doctor' => '',
            'address' => '', 'phone' => '', 'sex' => 'female', 'age' => '44', 'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test']);
        $this->doctor = Doctor::create(['name' => 'Dr Faraz', 'img_url' => '', 'email' => '', 'address' => '', 'phone' => '',
            'department' => '', 'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '']);
        $svc = PaymentCategory::create(['category' => 'Ultrasound abdomen', 'c_price' => '2500']);
        $this->us = RadiologyExam::create(['name' => 'Ultrasound abdomen', 'modality' => 'ultrasound', 'payment_category_id' => $svc->id,
            'report_template' => "Liver:\nGall bladder:\nKidneys:", 'active' => true]);
    }

    public function test_ordering_with_billing_raises_one_invoice(): void
    {
        $this->post('/radiology', ['patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'exams' => [$this->us->id], 'urgent' => 1, 'bill' => 1])
            ->assertSessionHasNoErrors();

        $o = RadiologyOrder::first();
        $this->assertTrue($o->urgent);
        $this->assertSame("Liver:\nGall bladder:\nKidneys:", $o->findings);
        $this->assertSame('2500.00', (string) Payment::find($o->payment_id)->gross_total);
    }

    public function test_an_exam_is_billed_once(): void
    {
        $this->post('/radiology', ['patient' => $this->patient->id, 'exams' => [$this->us->id]]);
        $o = RadiologyOrder::first();
        $this->post('/radiology/' . $o->id . '/bill');
        $this->post('/radiology/' . $o->id . '/bill');

        $this->assertSame(1, Payment::count());
    }

    public function test_the_report_needs_an_impression_and_is_stamped(): void
    {
        $this->post('/radiology', ['patient' => $this->patient->id, 'exams' => [$this->us->id]]);
        $o = RadiologyOrder::first();

        $this->put('/radiology/' . $o->id, ['action' => 'report', 'findings' => 'Liver normal'])->assertSessionHasErrors('impression');
        $this->put('/radiology/' . $o->id, ['action' => 'report', 'findings' => 'Liver normal', 'impression' => 'Normal study'])->assertSessionHasNoErrors();

        $o = $o->fresh();
        $this->assertSame('reported', $o->status);
        $this->assertNotNull($o->reported_at);
        $this->assertNotNull($o->performed_at);
    }

    public function test_a_reported_exam_cannot_be_cancelled(): void
    {
        $this->post('/radiology', ['patient' => $this->patient->id, 'exams' => [$this->us->id]]);
        $o = RadiologyOrder::first();
        $this->put('/radiology/' . $o->id, ['action' => 'report', 'impression' => 'Normal']);
        $this->put('/radiology/' . $o->id, ['action' => 'cancel'])->assertSessionHasErrors('action');
    }

    public function test_the_worklist_shows_open_orders_urgent_first(): void
    {
        $this->post('/radiology', ['patient' => $this->patient->id, 'exams' => [$this->us->id]]);
        $this->post('/radiology', ['patient' => $this->patient->id, 'exams' => [$this->us->id], 'urgent' => 1]);
        $orders = $this->get('/radiology')->assertOk()->viewData('orders');

        $this->assertTrue($orders->first()->urgent);
    }

    public function test_radiology_can_be_ordered_from_the_consultation(): void
    {
        $start = now()->setTime(9, 0);
        $v = Appointment::create(['patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'date' => $start->toDateString(),
            'time_slot' => '09:00 - 09:15', 's_time' => $start->toDateTimeString(), 'e_time' => $start->copy()->addMinutes(15)->toDateTimeString(),
            's_time_key' => $start->format('YmdHi'), 'status' => 'arrived', 'remarks' => '', 'add_date' => $start->toDateString(),
            'registration_time' => now()->toDateTimeString(), 'user' => '', 'request' => '']);

        $this->post('/consultations/visit/' . $v->id, ['provisional_diagnosis' => 'Cholecystitis?', 'radiology_exams' => [$this->us->id]])->assertSessionHasNoErrors();

        $o = RadiologyOrder::first();
        $this->assertSame($v->id, (int) $o->appointment_id);
        $this->assertSame('Cholecystitis?', $o->clinical_notes);
    }

    public function test_screens_render(): void
    {
        $this->post('/radiology', ['patient' => $this->patient->id, 'exams' => [$this->us->id]]);
        $o = RadiologyOrder::first();
        $this->put('/radiology/' . $o->id, ['action' => 'report', 'impression' => 'Gallstones']);
        $this->get('/radiology/create')->assertOk()->assertSee('Ultrasound abdomen');
        $this->get('/radiology/' . $o->id)->assertOk()->assertSee('Gallstones');
        $this->get('/radiology/' . $o->id . '/print')->assertOk()->assertSee('Radiology report');
        $this->get('/radiology/catalogue')->assertOk();
        $this->get('/patients/' . $this->patient->id)->assertOk()->assertSee('Radiology');
    }
}
