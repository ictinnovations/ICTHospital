<?php
/**
 * ICTHospital - appointment module feature tests.
 *
 * The clash check is the reason this module exists as more than a form, so most
 * of these cover it.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $otherPatient;
    private $doctor;
    private $otherDoctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Staff', 'login' => 'teststaff',
            'email' => 'staff@example.test', 'desc' => 'front desk', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = $this->patient('Amina Yousaf', '000001');
        $this->otherPatient = $this->patient('Bilal Ahmed', '000002');
        $this->doctor = $this->doctor('Dr Nadia Aslam');
        $this->otherDoctor = $this->doctor('Dr Imran Shah');
    }

    private function patient($name, $ref)
    {
        return Patient::create([
            'name' => $name, 'patient_id' => $ref, 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
    }

    private function doctor($name)
    {
        return Doctor::create([
            'name' => $name, 'img_url' => '', 'email' => '', 'address' => '',
            'phone' => '', 'department' => '', 'profile' => '', 'x' => '', 'y' => '',
            'ion_user_id' => '',
        ]);
    }

    private function booking(array $overrides = [])
    {
        return array_merge([
            'patient' => $this->patient->id,
            'doctor' => $this->doctor->id,
            'date' => '2026-10-01',
            'start' => '09:00',
            'minutes' => 30,
            'status' => 'scheduled',
        ], $overrides);
    }

    public function test_booking_derives_the_end_time_and_the_slot_label(): void
    {
        $this->post('/appointments', $this->booking())->assertSessionHasNoErrors();

        $appointment = Appointment::first();
        $this->assertNotNull($appointment);
        $this->assertSame('2026-10-01 09:00:00', (string) $appointment->s_time);
        $this->assertSame('2026-10-01 09:30:00', (string) $appointment->e_time);
        $this->assertSame('09:00 - 09:30', $appointment->time_slot);
        $this->assertSame('202610010900', $appointment->s_time_key);
    }

    public function test_a_doctor_cannot_be_double_booked(): void
    {
        $this->post('/appointments', $this->booking())->assertSessionHasNoErrors();

        // Overlaps the first by fifteen minutes.
        $this->post('/appointments', $this->booking([
            'patient' => $this->otherPatient->id,
            'start' => '09:15',
        ]))->assertSessionHasErrors('start');

        $this->assertSame(1, Appointment::count());
    }

    public function test_back_to_back_appointments_are_allowed(): void
    {
        $this->post('/appointments', $this->booking())->assertSessionHasNoErrors();

        // Starts exactly when the first ends, which is how a clinic runs.
        $this->post('/appointments', $this->booking([
            'patient' => $this->otherPatient->id,
            'start' => '09:30',
        ]))->assertSessionHasNoErrors();

        $this->assertSame(2, Appointment::count());
    }

    public function test_a_cancelled_appointment_frees_the_slot(): void
    {
        $this->post('/appointments', $this->booking(['status' => 'cancelled']))
            ->assertSessionHasNoErrors();

        $this->post('/appointments', $this->booking(['patient' => $this->otherPatient->id]))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Appointment::count());
    }

    public function test_two_doctors_can_hold_the_same_slot(): void
    {
        $this->post('/appointments', $this->booking())->assertSessionHasNoErrors();

        $this->post('/appointments', $this->booking([
            'doctor' => $this->otherDoctor->id,
            'patient' => $this->otherPatient->id,
        ]))->assertSessionHasNoErrors();

        $this->assertSame(2, Appointment::count());
    }

    public function test_editing_an_appointment_does_not_clash_with_itself(): void
    {
        $this->post('/appointments', $this->booking());
        $appointment = Appointment::first();

        $this->put('/appointments/' . $appointment->id, $this->booking(['status' => 'arrived']))
            ->assertSessionHasNoErrors();

        $this->assertSame('arrived', $appointment->fresh()->status);
    }

    public function test_booking_requires_a_real_patient_and_doctor(): void
    {
        $this->post('/appointments', $this->booking(['patient' => 9999]))
            ->assertSessionHasErrors('patient');

        $this->post('/appointments', $this->booking(['doctor' => 9999]))
            ->assertSessionHasErrors('doctor');

        $this->assertSame(0, Appointment::count());
    }

    public function test_the_day_view_only_shows_the_requested_date(): void
    {
        $this->post('/appointments', $this->booking());
        $this->post('/appointments', $this->booking([
            'patient' => $this->otherPatient->id,
            'date' => '2026-10-02',
        ]));

        $this->get('/appointments?date=2026-10-01')
            ->assertSee('Amina Yousaf')
            ->assertDontSee('Bilal Ahmed');
    }
}
