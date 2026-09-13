<?php
/**
 * ICTHospital - the writes that had no lock.
 *
 * Appointments and admissions already took a row lock. Pharmacy stock, invoice
 * payment and patient id allocation did not, and each could be made to misbehave by
 * two people pressing a button at the same moment.
 *
 * An honest caveat about what these prove. The suite runs on sqlite, where
 * lockForUpdate is a no-op, so nothing here can actually interleave two requests and
 * demonstrate the race. What they hold shut is everything around it: the check and
 * the write are in one transaction, a rejected write leaves no partial state, and the
 * arithmetic is right. The locks themselves are exercised by MySQL and MariaDB in
 * production, and are reviewable in the three controllers.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\InvoicePayment;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PharmacyPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcurrentWriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Desk', 'login' => 'lockdesk',
            'email' => 'locks@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));
    }

    private function patient($id = '000001', $name = 'Race Patient')
    {
        return Patient::create([
            'name' => $name, 'patient_id' => $id, 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
    }

    /* ---------------- patient id allocation ---------------- */

    public function test_registrations_get_consecutive_ids(): void
    {
        $this->post('/patients', ['name' => 'First']);
        $this->post('/patients', ['name' => 'Second']);
        $this->post('/patients', ['name' => 'Third']);

        $this->assertSame(
            ['000001', '000002', '000003'],
            Patient::orderBy('id')->pluck('patient_id')->all()
        );
    }

    /**
     * The id comes from MAX(patient_id) + 1 rather than a row count, so a gap in the
     * middle stays a gap. Deleting the highest one and reissuing that number is fine,
     * since nobody holds it; handing out a number a surviving record already has is
     * the failure this guards against.
     */
    public function test_a_deleted_patient_number_in_the_middle_is_not_reissued(): void
    {
        $this->post('/patients', ['name' => 'First']);
        $this->post('/patients', ['name' => 'Second']);
        $this->post('/patients', ['name' => 'Third']);

        Patient::where('patient_id', '000002')->delete();

        $this->post('/patients', ['name' => 'Fourth']);

        $this->assertSame('000004', Patient::orderByDesc('id')->value('patient_id'));
        $this->assertSame(0, Patient::where('patient_id', '000002')->count());
        $this->assertSame(
            Patient::count(),
            Patient::distinct()->count('patient_id'),
            'a patient_id was issued twice'
        );
    }

    public function test_every_issued_patient_id_is_unique(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/patients', ['name' => 'Patient ' . $i]);
        }

        $ids = Patient::pluck('patient_id');
        $this->assertSame($ids->count(), $ids->unique()->count());
    }

    /* ---------------- pharmacy stock ---------------- */

    public function test_a_sale_cannot_take_more_than_is_in_stock(): void
    {
        $medicine = Medicine::create([
            'name' => 'Amoxicillin', 'quantity' => 5, 's_price' => '20.00', 'b_price' => '15.00',
        ]);

        $this->post('/pharmacy', [
            'date' => '2026-10-01',
            'items' => [['medicine_id' => $medicine->id, 'quantity' => 6]],
        ])->assertSessionHasErrors('items');

        $this->assertSame(5, (int) $medicine->fresh()->quantity);
        $this->assertSame(0, PharmacyPayment::count());
    }

    public function test_a_rejected_sale_leaves_no_partial_record(): void
    {
        $ok = Medicine::create([
            'name' => 'Paracetamol', 'quantity' => 50, 's_price' => '5.00', 'b_price' => '3.00',
        ]);
        $short = Medicine::create([
            'name' => 'Ibuprofen', 'quantity' => 1, 's_price' => '8.00', 'b_price' => '5.00',
        ]);

        // The first line is fine, the second is not. Nothing may be written.
        $this->post('/pharmacy', [
            'date' => '2026-10-01',
            'items' => [
                ['medicine_id' => $ok->id, 'quantity' => 2],
                ['medicine_id' => $short->id, 'quantity' => 5],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(0, PharmacyPayment::count());
        $this->assertSame(50, (int) $ok->fresh()->quantity);
        $this->assertSame(1, (int) $short->fresh()->quantity);
    }

    public function test_stock_never_goes_below_zero_across_repeated_sales(): void
    {
        $medicine = Medicine::create([
            'name' => 'Metformin', 'quantity' => 10, 's_price' => '4.00', 'b_price' => '2.00',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/pharmacy', [
                'date' => '2026-10-01',
                'items' => [['medicine_id' => $medicine->id, 'quantity' => 3]],
            ]);
        }

        // Three sales of 3 fit in 10; the fourth and fifth must be refused.
        $this->assertSame(1, (int) $medicine->fresh()->quantity);
        $this->assertGreaterThanOrEqual(0, (int) $medicine->fresh()->quantity);
        $this->assertSame(3, PharmacyPayment::count());
    }

    /* ---------------- invoice payment ---------------- */

    private function invoice($total = '5000.00')
    {
        return Payment::create([
            'patient' => $this->patient()->id, 'date' => '2026-10-01',
            'gross_total' => $total, 'amount' => $total, 'vat' => '0', 'discount' => '0',
        ]);
    }

    public function test_an_invoice_cannot_be_overpaid(): void
    {
        $invoice = $this->invoice('1000.00');

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '600'])
            ->assertSessionHasNoErrors();
        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '600'])
            ->assertSessionHasErrors('amount');

        $this->assertSame(1, InvoicePayment::where('payment_id', $invoice->id)->count());
        $this->assertEqualsWithDelta(400.0, $invoice->fresh('payments')->balance(), 0.001);
    }

    public function test_part_payments_settle_an_invoice_exactly(): void
    {
        $invoice = $this->invoice('900.00');

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '300']);
        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '300']);
        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '300'])
            ->assertSessionHasNoErrors();

        $fresh = $invoice->fresh('payments');
        $this->assertEqualsWithDelta(0.0, $fresh->balance(), 0.001);
        $this->assertSame('paid', $fresh->settlement());
    }

    public function test_a_refused_payment_is_not_recorded_at_all(): void
    {
        $invoice = $this->invoice('500.00');

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '900'])
            ->assertSessionHasErrors('amount');

        $this->assertSame(0, InvoicePayment::where('payment_id', $invoice->id)->count());
        $this->assertEqualsWithDelta(500.0, $invoice->fresh('payments')->balance(), 0.001);
        $this->assertSame('unpaid', $invoice->fresh('payments')->settlement());
    }

    public function test_the_legacy_amount_received_column_follows_the_payment_rows(): void
    {
        $invoice = $this->invoice('800.00');

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '250']);
        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => '150']);

        $this->assertSame('400.00', $invoice->fresh()->amount_received);
        $this->assertSame('part paid', $invoice->fresh('payments')->settlement());
    }
}
