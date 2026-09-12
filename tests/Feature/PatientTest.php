<?php
/**
 * ICTHospital - patient module feature tests.
 *
 * The route smoke test only issues GETs, so it proves the pages render and
 * nothing more. These cover the write path: registration, the derived age, the
 * sequential patient ID, and the search box.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The User model has no factory. It predates the rebuild and its table keeps
     * the legacy column set (login, group, desc), so the row is built by hand
     * rather than bending a factory around a schema this old.
     */
    private function actingAsStaff()
    {
        return $this->actingAs(User::create([
            'firstname' => 'Test',
            'lastname' => 'Staff',
            'login' => 'teststaff',
            'email' => 'staff@example.test',
            'desc' => 'front desk',
            'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));
    }

    public function test_registration_stores_a_patient_and_derives_the_age(): void
    {
        $response = $this->actingAsStaff()->post('/patients', [
            'name' => 'Amina Yousaf',
            'birthdate' => now()->subYears(34)->subDays(2)->toDateString(),
            'phone' => '03001234567',
            'sex' => 'female',
            'bloodgroup' => 'O+',
        ]);

        $patient = Patient::first();
        $this->assertNotNull($patient, 'the patient row was not written');
        $response->assertRedirect('/patients/' . $patient->id);

        $this->assertSame('Amina Yousaf', $patient->name);
        $this->assertSame('34', $patient->age, 'age should be derived from birthdate, not typed');
        $this->assertSame('front desk', $patient->how_added);
        $this->assertNotEmpty($patient->registration_time);
    }

    public function test_name_is_the_only_required_field(): void
    {
        // A walk-in with nothing but a name still has to be registerable.
        $this->actingAsStaff()->post('/patients', ['name' => 'Unknown Male'])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Patient::count());
        $this->assertSame('', Patient::first()->age, 'no birthdate means no age, not a guess');
    }

    public function test_registration_rejects_a_missing_name(): void
    {
        $this->actingAsStaff()->post('/patients', ['phone' => '03001234567'])
            ->assertSessionHasErrors('name');

        $this->assertSame(0, Patient::count());
    }

    public function test_patient_ids_do_not_repeat_after_a_deletion(): void
    {
        $staff = $this->actingAsStaff();
        $staff->post('/patients', ['name' => 'First Patient']);
        $staff->post('/patients', ['name' => 'Second Patient']);

        $second = Patient::where('name', 'Second Patient')->first();
        $this->assertSame('000002', $second->patient_id);

        // Deleting the newest must not hand its number to the next registration,
        // which is what a count-based ID would do.
        $staff->delete('/patients/' . $second->id);
        $staff->post('/patients', ['name' => 'Third Patient']);

        $third = Patient::where('name', 'Third Patient')->first();
        $this->assertSame('000002', $third->patient_id);
        $this->assertSame(2, Patient::count());
    }

    public function test_search_matches_name_phone_and_patient_id(): void
    {
        $staff = $this->actingAsStaff();
        $staff->post('/patients', ['name' => 'Bilal Ahmed', 'phone' => '03119876543']);
        $staff->post('/patients', ['name' => 'Sara Khan', 'phone' => '03007654321']);

        $staff->get('/patients?q=Bilal')->assertSee('Bilal Ahmed')->assertDontSee('Sara Khan');
        $staff->get('/patients?q=03007654321')->assertSee('Sara Khan')->assertDontSee('Bilal Ahmed');
        $staff->get('/patients?q=000001')->assertSee('Bilal Ahmed');
    }

    public function test_editing_updates_the_record_and_recalculates_the_age(): void
    {
        $staff = $this->actingAsStaff();
        $staff->post('/patients', ['name' => 'Imran Shah', 'birthdate' => now()->subYears(20)->toDateString()]);
        $patient = Patient::first();
        $this->assertSame('20', $patient->age);

        $staff->put('/patients/' . $patient->id, [
            'name' => 'Imran Shah',
            'birthdate' => now()->subYears(41)->toDateString(),
        ]);

        $this->assertSame('41', $patient->fresh()->age);
    }
}
