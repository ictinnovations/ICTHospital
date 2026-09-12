<?php
/**
 * ICTHospital - laboratory feature tests.
 *
 * The derived status and the part reported state are the reason the module was
 * built this way, so most of these check those.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Lab;
use App\Models\LabCategory;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabRequestTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $hb;
    private $culture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Tech', 'login' => 'labtech',
            'email' => 'lab@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Amina Yousaf', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '03001234567', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);

        $this->hb = LabCategory::create([
            'category' => 'Haemoglobin', 'reference_value' => '13.5 to 17.5 g/dL',
        ]);
        $this->culture = LabCategory::create(['category' => 'Blood culture']);
    }

    private function raise(array $tests = null)
    {
        return $this->post('/lab', [
            'patient' => $this->patient->id,
            'date' => '2026-10-01',
            'tests' => $tests ?? [$this->hb->id, $this->culture->id],
        ]);
    }

    public function test_raising_a_request_creates_one_row_per_test(): void
    {
        $this->raise()->assertSessionHasNoErrors();

        $lab = Lab::first();
        $this->assertNotNull($lab);
        $this->assertSame(2, $lab->tests()->count());
        $this->assertSame('requested', $lab->status);
    }

    public function test_the_reference_range_is_copied_onto_the_test(): void
    {
        $this->raise([$this->hb->id]);

        $test = LabTest::first();
        $this->assertSame('13.5 to 17.5 g/dL', $test->reference_value);

        // Changing the catalogue afterwards must not rewrite history.
        $this->hb->update(['reference_value' => '12 to 16 g/dL']);
        $this->assertSame('13.5 to 17.5 g/dL', $test->fresh()->reference_value);
    }

    public function test_a_request_needs_at_least_one_test(): void
    {
        $this->raise([])->assertSessionHasErrors('tests');
        $this->assertSame(0, Lab::count());
    }

    public function test_status_becomes_partial_then_reported(): void
    {
        $this->raise();
        $lab = Lab::first();
        $hbTest = $lab->tests()->where('name', 'Haemoglobin')->first();
        $cultureTest = $lab->tests()->where('name', 'Blood culture')->first();

        $this->post('/lab/' . $lab->id . '/results', ['results' => [$hbTest->id => '14.2 g/dL']]);
        $this->assertSame('partial', $lab->fresh()->status);

        $this->post('/lab/' . $lab->id . '/results', [
            'results' => [$hbTest->id => '14.2 g/dL', $cultureTest->id => 'No growth'],
        ]);
        $this->assertSame('reported', $lab->fresh()->status);
        $this->assertNotNull($lab->fresh()->report_date);
    }

    public function test_a_blank_box_does_not_wipe_a_recorded_result(): void
    {
        $this->raise([$this->hb->id]);
        $lab = Lab::first();
        $test = $lab->tests()->first();

        $this->post('/lab/' . $lab->id . '/results', ['results' => [$test->id => '14.2 g/dL']]);

        // Submitting from a stale page, with the box empty.
        $this->post('/lab/' . $lab->id . '/results', ['results' => [$test->id => '']]);

        $this->assertSame('14.2 g/dL', $test->fresh()->result);
    }

    public function test_the_legacy_summary_columns_are_kept_in_step(): void
    {
        $this->raise();
        $lab = Lab::first();
        $this->assertStringContainsString('Haemoglobin', $lab->category_name);
        $this->assertStringContainsString('pending', $lab->report);

        $hbTest = $lab->tests()->where('name', 'Haemoglobin')->first();
        $this->post('/lab/' . $lab->id . '/results', ['results' => [$hbTest->id => '14.2 g/dL']]);

        $this->assertStringContainsString('Haemoglobin: 14.2 g/dL', $lab->fresh()->report);
    }

    public function test_the_patient_details_are_denormalised_onto_the_request(): void
    {
        $this->raise();

        // The legacy columns feed old exports, so they have to be filled.
        $this->assertSame('Amina Yousaf', Lab::first()->patient_name);
        $this->assertSame('03001234567', Lab::first()->patient_phone);
    }

    public function test_outstanding_and_reported_lists_separate_correctly(): void
    {
        $this->raise([$this->hb->id]);
        $lab = Lab::first();

        $this->get('/lab?status=pending')->assertSee('Amina Yousaf');
        $this->get('/lab?status=reported')->assertSee('Nothing to show');

        $this->post('/lab/' . $lab->id . '/results', ['results' => [$lab->tests()->first()->id => '14.2']]);

        $this->get('/lab?status=reported')->assertSee('Amina Yousaf');
        $this->get('/lab?status=pending')->assertSee('Nothing to show');
    }

    public function test_a_requested_test_cannot_be_deleted_from_the_catalogue(): void
    {
        $this->raise([$this->hb->id]);

        $this->delete('/lab/catalogue/' . $this->hb->id);

        $this->assertNotNull(LabCategory::find($this->hb->id));
    }

    public function test_the_catalogue_refuses_a_duplicate_test_name(): void
    {
        $this->post('/lab/catalogue', ['category' => 'Haemoglobin'])
            ->assertSessionHasErrors('category');

        $this->assertSame(2, LabCategory::count());
    }
}
