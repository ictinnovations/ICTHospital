<?php
/**
 * ICTHospital - prescription and medicine catalogue feature tests.
 *
 * The point of the module is that a prescription's drugs are rows rather than a
 * packed string, so most of these check that the rows exist, stay readable, and
 * can be queried.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $doctor;
    private $amox;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Doc', 'login' => 'testdoc',
            'email' => 'doc@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Amina Yousaf', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);

        $this->doctor = Doctor::create([
            'name' => 'Dr Nadia Aslam', 'img_url' => '', 'email' => '', 'address' => '',
            'phone' => '', 'department' => '', 'profile' => '', 'x' => '', 'y' => '',
            'ion_user_id' => '',
        ]);

        $this->amox = Medicine::create(['name' => 'Amoxil', 'generic' => 'Amoxicillin', 'quantity' => 10]);
    }

    private function script(array $overrides = [])
    {
        return array_merge([
            'patient' => $this->patient->id,
            'doctor' => $this->doctor->id,
            'date' => '2026-10-01',
            'items' => [
                ['medicine_id' => $this->amox->id, 'dosage' => '1+0+1', 'duration' => '5 days'],
            ],
        ], $overrides);
    }

    public function test_a_prescription_stores_its_drugs_as_rows(): void
    {
        $this->post('/prescriptions', $this->script())->assertSessionHasNoErrors();

        $prescription = Prescription::first();
        $this->assertNotNull($prescription);
        $this->assertSame(1, $prescription->items()->count());

        $item = $prescription->items()->first();
        $this->assertSame('Amoxil', $item->name, 'the catalogue name is copied onto the line');
        $this->assertSame('1+0+1', $item->dosage);
    }

    public function test_the_legacy_summary_column_is_kept_in_step(): void
    {
        $this->post('/prescriptions', $this->script());

        $this->assertSame(
            'Amoxil 1+0+1 for 5 days',
            Prescription::first()->medicine,
            'anything still reading the old column should see something readable'
        );
    }

    public function test_a_prescription_needs_at_least_one_medicine(): void
    {
        $this->post('/prescriptions', $this->script(['items' => []]))
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Prescription::count());
    }

    public function test_blank_medicine_rows_are_dropped(): void
    {
        $this->post('/prescriptions', $this->script([
            'items' => [
                ['medicine_id' => $this->amox->id, 'dosage' => '1+0+1'],
                ['medicine_id' => '', 'name' => '', 'dosage' => ''],
            ],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(1, PrescriptionMedicine::count());
    }

    public function test_a_drug_outside_the_catalogue_can_still_be_written(): void
    {
        $this->post('/prescriptions', $this->script([
            'items' => [['medicine_id' => '', 'name' => 'Imported antifungal', 'dosage' => '1+0+0']],
        ]))->assertSessionHasNoErrors();

        $item = PrescriptionMedicine::first();
        $this->assertSame('Imported antifungal', $item->name);
        $this->assertNull($item->medicine_id);
    }

    public function test_prescriptions_can_be_searched_by_drug(): void
    {
        $this->post('/prescriptions', $this->script());

        // The query the packed free text column made impossible.
        $this->get('/prescriptions?drug=Amoxil')->assertSee('Amina Yousaf');
        $this->get('/prescriptions?drug=Paracetamol')->assertSee('Nothing matches');
    }

    public function test_editing_replaces_the_drug_list(): void
    {
        $this->post('/prescriptions', $this->script());
        $prescription = Prescription::first();

        $this->put('/prescriptions/' . $prescription->id, $this->script([
            'items' => [['medicine_id' => '', 'name' => 'Paracetamol', 'dosage' => '1+1+1']],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(1, PrescriptionMedicine::count(), 'the old line should be gone, not kept alongside');
        $this->assertSame('Paracetamol', PrescriptionMedicine::first()->name);
    }

    public function test_a_prescribed_medicine_cannot_be_deleted_from_the_catalogue(): void
    {
        $this->post('/prescriptions', $this->script());

        $this->delete('/medicines/' . $this->amox->id);

        $this->assertNotNull(Medicine::find($this->amox->id));
    }

    public function test_the_catalogue_refuses_a_duplicate_name_and_pack(): void
    {
        $this->post('/medicines', ['name' => 'Panadol', 'box' => '500mg x 10', 'quantity' => 5])
            ->assertSessionHasNoErrors();

        $this->post('/medicines', ['name' => 'Panadol', 'box' => '500mg x 10', 'quantity' => 5])
            ->assertSessionHasErrors('name');

        // A different pack size is a different line, not a duplicate.
        $this->post('/medicines', ['name' => 'Panadol', 'box' => '500mg x 20', 'quantity' => 5])
            ->assertSessionHasNoErrors();

        $this->assertSame(3, Medicine::count());
    }

    public function test_a_category_with_medicines_in_it_cannot_be_removed(): void
    {
        $category = MedicineCategory::create(['category' => 'Antibiotics']);
        $this->post('/medicines', ['name' => 'Zithro', 'category' => $category->id, 'quantity' => 1]);

        $this->delete('/medicines/categories/' . $category->id);

        $this->assertNotNull(MedicineCategory::find($category->id));
    }
}
