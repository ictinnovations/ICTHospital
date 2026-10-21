<?php
/**
 * ICTHospital - dashboard feature tests.
 *
 * appointment.patient and appointment.doctor hold row ids. The recent appointments
 * box used to print those ids, so a front desk saw "5" and "2" instead of names.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_recent_appointments_show_names_not_ids(): void
    {
        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Desk', 'login' => 'desk',
            'email' => 'desk@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $patient = Patient::create([
            'name' => 'Hina Malik', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
        $doctor = Doctor::create([
            'name' => 'Dr Rehan Ali', 'img_url' => '', 'email' => '', 'address' => '',
            'phone' => '', 'department' => '', 'profile' => '', 'x' => '', 'y' => '',
            'ion_user_id' => '',
        ]);

        $this->post('/appointments', [
            'patient' => $patient->id, 'doctor' => $doctor->id, 'date' => '2026-10-01',
            'start' => '09:00', 'minutes' => 15, 'status' => 'scheduled',
        ])->assertSessionHasNoErrors();
        $this->flushSession();

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Hina Malik')
            ->assertSee('Dr Rehan Ali')
            ->assertSee('/patients/' . $patient->id, false);
    }
}
