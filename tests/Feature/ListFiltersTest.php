<?php
/**
 * ICTHospital - list filters submitted with blank fields.
 *
 * A filter form sends every field, and Laravel turns a blank one into null. The lists
 * compared against '' and so filtered on "status IS NULL" and similar, which returned
 * nothing whenever "All doctors" or "Any status" was left selected (found on the demo,
 * 22 October 2026). Each list must treat a blank field as "all".
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

class ListFiltersTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Admin', 'login' => 'filters',
            'email' => 'filters@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret123'),
        ]));
        $this->patient = Patient::create([
            'name' => 'Amina Yousaf', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
        $this->doctor = Doctor::create([
            'name' => 'Dr Sana Riaz', 'img_url' => '', 'email' => '', 'address' => '',
            'phone' => '', 'department' => '', 'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '',
        ]);
        $this->post('/appointments', [
            'patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'date' => now()->toDateString(),
            'start' => '09:00', 'minutes' => 15, 'status' => 'scheduled',
        ])->assertSessionHasNoErrors();
        $this->flushSession();
    }

    public function test_the_appointment_day_list_treats_blank_filters_as_all(): void
    {
        $this->get('/appointments?date=&doctor=&status=')->assertOk()->assertSee('Amina Yousaf');
        $this->get('/appointments?date=' . now()->toDateString() . '&doctor=' . $this->doctor->id . '&status=')
            ->assertOk()->assertSee('Amina Yousaf');
        $this->get('/appointments?date=' . now()->toDateString() . '&doctor=&status=scheduled')
            ->assertOk()->assertSee('Amina Yousaf');
    }

    public function test_filtering_by_another_doctor_still_excludes_the_appointment(): void
    {
        $other = Doctor::create([
            'name' => 'Dr Imran Bashir', 'img_url' => '', 'email' => '', 'address' => '',
            'phone' => '', 'department' => '', 'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '',
        ]);

        $this->get('/appointments?date=' . now()->toDateString() . '&doctor=' . $other->id . '&status=')
            ->assertOk()->assertDontSee('Amina Yousaf');
    }

    public function test_other_lists_accept_blank_filters(): void
    {
        foreach (['/prescriptions?patient=&doctor=&drug=', '/invoices?patient=&show=all', '/medicines?q=&category=',
                  '/beds?category=', '/lab?patient=&status='] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
