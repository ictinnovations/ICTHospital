<?php
/**
 * ICTHospital - patient chart feature tests.
 *
 * The patient page brings together everything recorded against one patient: history
 * notes, OPD visits with vitals, ward stays and billing. These tests write each record
 * through its own module's route, as staff would, then check the chart shows it, adds
 * up the money correctly, and never shows another patient's records.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientChartTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $other;
    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Doctor', 'login' => 'chartdoc',
            'email' => 'chart@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = $this->patient('Amina Yousaf', '000001');
        $this->other = $this->patient('Bilal Ahmed', '000002');

        $this->doctor = Doctor::create([
            'name' => 'Dr Sana Riaz', 'img_url' => '', 'email' => '', 'address' => '',
            'phone' => '', 'department' => '', 'profile' => '', 'x' => '', 'y' => '',
            'ion_user_id' => '',
        ]);
    }

    private function patient($name, $ref)
    {
        return Patient::create([
            'name' => $name, 'patient_id' => $ref, 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
    }

    public function test_an_empty_chart_renders_every_section(): void
    {
        $this->get('/patients/' . $this->patient->id)
            ->assertOk()
            ->assertSee('Medical history')
            ->assertSee('OPD visits and vitals')
            ->assertSee('Prescriptions')
            ->assertSee('Lab results')
            ->assertSee('Admissions (IPD)')
            ->assertSee('Billing and payments')
            ->assertSee('No appointments yet.');
    }

    public function test_the_chart_shows_history_visits_and_vitals(): void
    {
        $this->post('/history', [
            'patient_id' => $this->patient->id, 'title' => 'Penicillin allergy',
            'description' => 'Rash after amoxicillin in 2024.', 'date' => '2026-09-01',
        ])->assertSessionHasNoErrors();

        $this->post('/appointments', [
            'patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'date' => '2026-10-01',
            'start' => '09:00', 'minutes' => 15, 'status' => 'arrived',
            'b_p' => '120/80', 'pulse' => '78', 'temprature' => '98.6', 'weight' => '64',
        ])->assertSessionHasNoErrors();

        $this->get('/patients/' . $this->patient->id)
            ->assertSee('Penicillin allergy')
            ->assertSee('120/80')
            ->assertSee('Dr Sana Riaz');
    }

    public function test_the_chart_shows_a_stay_on_the_ward(): void
    {
        $ward = BedCategory::create(['category' => 'General ward', 'description' => '']);
        $bed = Bed::create([
            'category' => $ward->id, 'number' => 'G7', 'description' => '',
            'status' => 'free', 'bed_id' => '', 'last_a_time' => null, 'last_d_time' => null,
        ]);

        $this->post('/admissions', ['patient' => $this->patient->id, 'bed_id' => $bed->id])
            ->assertSessionHasNoErrors();

        $this->get('/patients/' . $this->patient->id)
            ->assertSee('General ward')
            ->assertSee('G7')
            ->assertSee('On the ward')
            ->assertSee('Admitted');
    }

    public function test_billing_totals_add_up_across_invoices(): void
    {
        $consult = PaymentCategory::create(['category' => 'Consultation', 'c_price' => '500']);
        foreach ([1, 2] as $quantity) {
            $this->post('/invoices', [
                'patient' => $this->patient->id, 'date' => '2026-10-01',
                'items' => [['payment_category_id' => $consult->id, 'quantity' => $quantity]],
            ])->assertSessionHasNoErrors();
        }
        $first = Payment::orderBy('id')->first();
        $this->post('/invoices/' . $first->id . '/pay', ['amount' => 500])->assertSessionHasNoErrors();

        $response = $this->get('/patients/' . $this->patient->id);

        $this->assertSame(1500.0, $response->viewData('billed'));
        $this->assertSame(500.0, $response->viewData('paid'));
        $this->assertSame(1000.0, $response->viewData('outstanding'));
        $response->assertSee('Paid')->assertSee('Unpaid');
    }

    public function test_a_doctor_is_pointed_to_record_vitals_and_sees_only_allowed_actions(): void
    {
        $this->post('/appointments', [
            'patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'date' => '2026-10-01',
            'start' => '09:00', 'minutes' => 15, 'status' => 'arrived',
        ])->assertSessionHasNoErrors();

        foreach (['patient_view', 'appointment_view', 'appointment_update', 'prescription_add'] as $key) {
            \Illuminate\Support\Facades\DB::table('permission')
                ->where(['permission_group' => 'doctor', 'permission_name' => $key])
                ->update(['permission_type' => 'yes']);
        }
        \App\Support\Permissions::flush();
        $this->actingAs(User::create([
            'firstname' => 'Rehan', 'lastname' => 'Ali', 'login' => 'drrehan',
            'email' => 'rehan@example.test', 'desc' => '', 'group' => 'Doctor',
            'password' => bcrypt('secret'),
        ]));
        $this->flushSession();

        $this->get('/patients/' . $this->patient->id)
            ->assertOk()
            ->assertSee('Record vitals')
            ->assertSee('New prescription')
            ->assertDontSee('Book appointment')
            ->assertDontSee('New invoice');
    }

    public function test_another_patients_records_never_appear(): void
    {
        $this->post('/history', [
            'patient_id' => $this->other->id, 'title' => 'Asthma since childhood',
            'description' => 'Uses an inhaler.', 'date' => '2026-09-01',
        ])->assertSessionHasNoErrors();

        // The save confirmation names the note and would show on the next page; drop it
        // so the assertion is about the chart's own data.
        $this->flushSession();

        $this->get('/patients/' . $this->patient->id)
            ->assertDontSee('Asthma since childhood')
            ->assertSee('No history notes yet.');
    }
}
