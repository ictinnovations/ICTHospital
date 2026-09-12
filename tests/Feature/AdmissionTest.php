<?php
/**
 * ICTHospital - beds and admissions feature tests.
 *
 * The invariants are the point of the module: one patient per bed, one bed per
 * patient, and a discharge that actually frees the bed.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\AllotedBed;
use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionTest extends TestCase
{
    use RefreshDatabase;

    private $ward;
    private $bedA;
    private $bedB;
    private $alice;
    private $bob;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Staff', 'login' => 'wardstaff',
            'email' => 'ward@example.test', 'desc' => 'ward', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->ward = BedCategory::create(['category' => 'General', 'description' => '']);
        $this->bedA = $this->bed('1');
        $this->bedB = $this->bed('2');
        $this->alice = $this->patient('Alice Rahman', '000001');
        $this->bob = $this->patient('Bob Nawaz', '000002');
    }

    private function bed($number)
    {
        return Bed::create([
            'category' => $this->ward->id, 'number' => $number, 'description' => '',
            'status' => 'free', 'bed_id' => '', 'last_a_time' => null, 'last_d_time' => null,
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

    public function test_admitting_marks_the_bed_occupied(): void
    {
        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id])
            ->assertSessionHasNoErrors();

        $admission = AllotedBed::first();
        $this->assertNotNull($admission);
        $this->assertNull($admission->d_time, 'an open admission has no discharge time');
        $this->assertSame('occupied', $this->bedA->fresh()->status);
    }

    public function test_a_bed_cannot_hold_two_patients(): void
    {
        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id]);

        $this->post('/admissions', ['patient' => $this->bob->id, 'bed_id' => $this->bedA->id])
            ->assertSessionHasErrors('bed_id');

        $this->assertSame(1, AllotedBed::count());
    }

    public function test_a_patient_cannot_occupy_two_beds(): void
    {
        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id]);

        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedB->id])
            ->assertSessionHasErrors('patient');

        $this->assertSame(1, AllotedBed::count());
    }

    public function test_discharge_frees_the_bed_for_the_next_patient(): void
    {
        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id]);
        $admission = AllotedBed::first();

        $this->post('/admissions/' . $admission->id . '/discharge')->assertSessionHasNoErrors();

        $this->assertNotNull($admission->fresh()->d_time);
        $this->assertSame('free', $this->bedA->fresh()->status);

        $this->post('/admissions', ['patient' => $this->bob->id, 'bed_id' => $this->bedA->id])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, AllotedBed::count());
    }

    public function test_a_discharged_patient_can_be_readmitted(): void
    {
        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id]);
        $this->post('/admissions/' . AllotedBed::first()->id . '/discharge');

        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedB->id])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, AllotedBed::count());
    }

    public function test_discharging_twice_is_refused(): void
    {
        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id]);
        $admission = AllotedBed::first();
        $this->post('/admissions/' . $admission->id . '/discharge');
        $first = $admission->fresh()->d_time;

        $this->post('/admissions/' . $admission->id . '/discharge');

        $this->assertSame($first, $admission->fresh()->d_time, 'the first discharge time must stand');
    }

    public function test_discharge_cannot_predate_admission(): void
    {
        $this->post('/admissions', [
            'patient' => $this->alice->id,
            'bed_id' => $this->bedA->id,
            'a_time' => '2026-10-05 09:00:00',
        ]);

        $this->post('/admissions/' . AllotedBed::first()->id . '/discharge', [
            'd_time' => '2026-10-04 09:00:00',
        ])->assertSessionHasErrors('d_time');

        $this->assertNull(AllotedBed::first()->d_time);
    }

    public function test_an_occupied_bed_cannot_be_deleted(): void
    {
        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id]);

        $this->delete('/beds/' . $this->bedA->id);

        $this->assertNotNull(Bed::find($this->bedA->id), 'the bed should still be there');
    }

    public function test_bed_numbers_are_unique_within_a_ward_only(): void
    {
        $this->post('/beds', ['category' => $this->ward->id, 'number' => '1'])
            ->assertSessionHasErrors('number');

        $icu = BedCategory::create(['category' => 'ICU', 'description' => '']);
        $this->post('/beds', ['category' => $icu->id, 'number' => '1'])
            ->assertSessionHasNoErrors();

        $this->assertSame(3, Bed::count());
    }

    public function test_the_ward_board_reports_free_beds(): void
    {
        $this->get('/beds')->assertSee('2 free of 2');

        $this->post('/admissions', ['patient' => $this->alice->id, 'bed_id' => $this->bedA->id]);

        $this->get('/beds')->assertSee('1 free of 2')->assertSee('Alice Rahman');
    }
}
