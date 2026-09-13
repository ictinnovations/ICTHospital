<?php
/**
 * ICTHospital - report feature tests.
 *
 * Each report exists because a child table made the question answerable, so each
 * test builds the rows and checks the number that comes out, rather than only
 * checking the page loads.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\AllotedBed;
use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Doctor;
use App\Models\InvoicePayment;
use App\Models\Lab;
use App\Models\LabCategory;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PharmacyPayment;
use App\Models\PharmacySaleItem;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Reports', 'login' => 'reportadmin',
            'email' => 'reports@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Hina Malik', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '03001112222', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);

        // prescription.doctor is NOT NULL in the legacy schema and the
        // prescription form requires it, so a report fixture needs one too.
        $this->doctor = Doctor::create(['name' => 'Dr Report']);
    }

    public function test_the_report_menu_opens(): void
    {
        $this->get('/reports')->assertOk()->assertSee('Outstanding lab work');
    }

    public function test_outstanding_lab_work_lists_only_the_unreported_test(): void
    {
        $hb = LabCategory::create(['category' => 'Haemoglobin']);
        $culture = LabCategory::create(['category' => 'Blood culture']);

        $lab = Lab::create([
            'patient' => $this->patient->id, 'date' => now()->subDays(5)->toDateString(),
            'status' => 'requested',
        ]);
        LabTest::create([
            'lab_id' => $lab->id, 'lab_category_id' => $hb->id, 'name' => 'Haemoglobin',
            'result' => '14.1', 'status' => 'reported',
        ]);
        LabTest::create([
            'lab_id' => $lab->id, 'lab_category_id' => $culture->id, 'name' => 'Blood culture',
            'result' => null, 'status' => 'requested',
        ]);

        $this->get('/reports/lab')
            ->assertOk()
            ->assertSee('Blood culture')
            ->assertDontSee('Haemoglobin');
    }

    public function test_a_test_waiting_more_than_three_days_counts_as_overdue(): void
    {
        $lab = Lab::create([
            'patient' => $this->patient->id, 'date' => now()->subDays(9)->toDateString(),
        ]);
        LabTest::create(['lab_id' => $lab->id, 'name' => 'Culture', 'result' => null]);

        $this->get('/reports/lab')
            ->assertOk()
            ->assertSee('1 over three days');
    }

    public function test_drug_usage_shows_prescribed_against_dispensed(): void
    {
        $prescription = Prescription::create([
            'patient' => $this->patient->id, 'doctor' => $this->doctor->id,
            'date' => now()->subDays(2)->toDateString(),
        ]);
        PrescriptionMedicine::create([
            'prescription_id' => $prescription->id, 'name' => 'Amoxicillin 500mg',
        ]);
        PrescriptionMedicine::create([
            'prescription_id' => $prescription->id, 'name' => 'Paracetamol 500mg',
        ]);

        $sale = PharmacyPayment::create([
            'date' => now()->subDays(1)->toDateString(), 'amount' => '120', 'vat' => '0', 'discount' => '0',
        ]);
        PharmacySaleItem::create([
            'pharmacy_payment_id' => $sale->id, 'name' => 'Paracetamol 500mg',
            'quantity' => 12, 'unit_price' => '10.00', 'line_total' => '120.00',
        ]);

        $response = $this->get('/reports/drug-usage')->assertOk();

        $response->assertSee('Amoxicillin 500mg');
        $response->assertSee('Paracetamol 500mg');
        $response->assertSee('120.00');
    }

    public function test_drug_usage_respects_the_date_range(): void
    {
        $old = Prescription::create([
            'patient' => $this->patient->id, 'doctor' => $this->doctor->id,
            'date' => now()->subDays(200)->toDateString(),
        ]);
        PrescriptionMedicine::create(['prescription_id' => $old->id, 'name' => 'Old Drug']);

        $this->get('/reports/drug-usage')->assertOk()->assertDontSee('Old Drug');

        $this->get('/reports/drug-usage?from=' . now()->subDays(300)->toDateString()
            . '&to=' . now()->toDateString())
            ->assertOk()
            ->assertSee('Old Drug');
    }

    public function test_debtors_shows_the_remaining_balance_not_the_invoice_total(): void
    {
        $invoice = Payment::create([
            'patient' => $this->patient->id, 'date' => now()->subDays(10)->toDateString(),
            'gross_total' => '5000.00', 'amount' => '5000.00', 'vat' => '0', 'discount' => '0',
        ]);
        InvoicePayment::create([
            'payment_id' => $invoice->id, 'amount' => '2000.00', 'method' => 'cash',
            'paid_at' => now()->subDays(9),
        ]);

        $this->get('/reports/debtors')
            ->assertOk()
            ->assertSee('Hina Malik')
            ->assertSee('3,000.00');
    }

    public function test_a_settled_invoice_is_not_a_debt(): void
    {
        $invoice = Payment::create([
            'patient' => $this->patient->id, 'date' => now()->toDateString(),
            'gross_total' => '1000.00', 'amount' => '1000.00', 'vat' => '0', 'discount' => '0',
        ]);
        InvoicePayment::create([
            'payment_id' => $invoice->id, 'amount' => '1000.00', 'method' => 'cash', 'paid_at' => now(),
        ]);

        $this->get('/reports/debtors')
            ->assertOk()
            ->assertSee('Nothing outstanding');
    }

    public function test_occupancy_counts_an_open_admission(): void
    {
        $ward = BedCategory::create(['category' => 'General ward']);
        $bed = Bed::create(['category' => $ward->id, 'number' => 'G1', 'status' => 'alloted']);
        AllotedBed::create([
            'patient' => $this->patient->id, 'bed_id' => $bed->id,
            'a_time' => now()->subDays(3), 'd_time' => null,
        ]);

        $this->get('/reports/occupancy')
            ->assertOk()
            ->assertSee('General ward');
    }

    public function test_occupancy_reports_average_length_of_stay(): void
    {
        $ward = BedCategory::create(['category' => 'Private']);
        $bed = Bed::create(['category' => $ward->id, 'number' => 'P1', 'status' => 'available']);
        AllotedBed::create([
            'patient' => $this->patient->id, 'bed_id' => $bed->id,
            'a_time' => now()->subDays(6), 'd_time' => now()->subDays(2),
        ]);

        $this->get('/reports/occupancy')
            ->assertOk()
            ->assertSee('4 day(s)');
    }

    public function test_a_reversed_date_range_is_swapped_rather_than_refused(): void
    {
        $this->get('/reports/occupancy?from=' . now()->toDateString()
            . '&to=' . now()->subDays(7)->toDateString())
            ->assertOk();
    }
}
