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

    public function test_finance_includes_accounting_income_and_expenses(): void
    {
        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Accounts', 'login' => 'accounts',
            'email' => 'accounts@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));
        \Illuminate\Support\Facades\DB::table('accounting')->insert([
            ['name' => 'Donations', 'type' => 'Income', 'amount' => 10000, 'date' => '2026-10-01', 'description' => ''],
            ['name' => 'Utilities', 'type' => 'Expence', 'amount' => 2500, 'date' => '2026-10-01', 'description' => ''],
        ]);

        $response = $this->get('/dashboard')->assertOk();
        $this->assertEquals(10000.0, $response->viewData('income'));
        $this->assertEquals(2500.0, $response->viewData('expenses'));
        $this->assertEquals(7500.0, $response->viewData('balance'));
    }

    public function test_bed_occupancy_ignores_discharged_stays(): void
    {
        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Ward', 'login' => 'ward',
            'email' => 'ward@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));
        $ward = \App\Models\BedCategory::create(['category' => 'General', 'description' => '']);
        $beds = collect(['G1', 'G2', 'G3', 'G4'])->map(fn ($n) => \App\Models\Bed::create([
            'category' => $ward->id, 'number' => $n, 'description' => '',
            'status' => 'free', 'bed_id' => '', 'last_a_time' => null, 'last_d_time' => null,
        ]));
        $patients = collect(['Amina', 'Bilal'])->map(fn ($n, $i) => Patient::create([
            'name' => $n, 'patient_id' => '00000' . ($i + 1), 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]));

        // Two stays, one already discharged: one bed of four is occupied, not two.
        $this->post('/admissions', ['patient' => $patients[0]->id, 'bed_id' => $beds[0]->id])->assertSessionHasNoErrors();
        $this->post('/admissions', ['patient' => $patients[1]->id, 'bed_id' => $beds[1]->id])->assertSessionHasNoErrors();
        $stay = \App\Models\AllotedBed::where('patient', $patients[1]->id)->first();
        $this->post('/admissions/' . $stay->id . '/discharge', ['d_time' => now()->addMinute()->format('Y-m-d H:i')]);

        $response = $this->get('/dashboard')->assertOk();
        $this->assertSame(1, $response->viewData('occupiedBeds'));
        $this->assertSame(3, $response->viewData('freeBeds'));
    }
}
