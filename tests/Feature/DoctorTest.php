<?php
/**
 * ICTHospital - doctor register feature tests.
 *
 * The point of this module is that a clean install can be made usable, so the
 * first test is the one that would have failed before: adding a doctor with
 * nothing filled in but a name, on a database built from migrations alone.
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

class DoctorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Admin', 'login' => 'doctoradmin',
            'email' => 'doctors@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));
    }

    public function test_a_doctor_can_be_added_with_only_a_name(): void
    {
        $this->post('/doctors', ['name' => 'Dr Sana Riaz'])->assertSessionHasNoErrors();

        $doctor = Doctor::first();
        $this->assertNotNull($doctor);
        $this->assertSame('Dr Sana Riaz', $doctor->name);
    }

    public function test_the_name_is_required(): void
    {
        $this->post('/doctors', ['name' => ''])->assertSessionHasErrors('name');

        $this->assertSame(0, Doctor::count());
    }

    public function test_two_doctors_cannot_share_an_email_address(): void
    {
        $this->post('/doctors', ['name' => 'Dr One', 'email' => 'same@example.test']);
        $this->post('/doctors', ['name' => 'Dr Two', 'email' => 'same@example.test'])
            ->assertSessionHasErrors('email');

        $this->assertSame(1, Doctor::count());
    }

    public function test_a_blank_email_does_not_clash_with_another_blank_email(): void
    {
        $this->post('/doctors', ['name' => 'Dr One'])->assertSessionHasNoErrors();
        $this->post('/doctors', ['name' => 'Dr Two'])->assertSessionHasNoErrors();

        $this->assertSame(2, Doctor::count());
    }

    public function test_editing_a_doctor_keeps_their_own_email(): void
    {
        $this->post('/doctors', ['name' => 'Dr One', 'email' => 'one@example.test']);
        $doctor = Doctor::first();

        $this->put('/doctors/' . $doctor->id, [
            'name' => 'Dr One Updated',
            'email' => 'one@example.test',
            'department' => 'Cardiology',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Dr One Updated', $doctor->fresh()->name);
        $this->assertSame('Cardiology', $doctor->fresh()->department);
    }

    public function test_a_doctor_with_appointments_is_not_deleted(): void
    {
        $doctor = Doctor::create(['name' => 'Dr Busy']);
        $patient = Patient::create([
            'name' => 'Test Patient', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
        Appointment::create([
            'patient' => $patient->id, 'doctor' => $doctor->id, 'date' => '2026-10-01',
            'status' => 'booked',
        ]);

        $this->delete('/doctors/' . $doctor->id);

        $this->assertNotNull(Doctor::find($doctor->id));
    }

    public function test_a_doctor_with_no_appointments_is_deleted(): void
    {
        $doctor = Doctor::create(['name' => 'Dr Locum']);

        $this->delete('/doctors/' . $doctor->id);

        $this->assertNull(Doctor::find($doctor->id));
    }

    public function test_the_list_can_be_searched_by_department(): void
    {
        Doctor::create(['name' => 'Dr Heart', 'department' => 'Cardiology']);
        Doctor::create(['name' => 'Dr Bone', 'department' => 'Orthopaedics']);

        $this->get('/doctors?q=Cardio')
            ->assertOk()
            ->assertSee('Dr Heart')
            ->assertDontSee('Dr Bone');
    }
}
