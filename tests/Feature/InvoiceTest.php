<?php
/**
 * ICTHospital - invoicing feature tests.
 *
 * The derived settlement state and part payment are the reason the module exists
 * in this shape, so that is what most of these cover.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $consult;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Cashier', 'login' => 'cashier',
            'email' => 'cash@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Amina Yousaf', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);

        $this->consult = PaymentCategory::create(['category' => 'Consultation', 'c_price' => '500']);
    }

    private function raise(array $items = null, array $extra = [])
    {
        return $this->post('/invoices', array_merge([
            'patient' => $this->patient->id,
            'date' => '2026-10-01',
            'items' => $items ?? [['payment_category_id' => $this->consult->id, 'quantity' => 1]],
        ], $extra));
    }

    public function test_an_invoice_stores_its_charges_as_rows(): void
    {
        $this->raise()->assertSessionHasNoErrors();

        $invoice = Payment::first();
        $this->assertNotNull($invoice);
        $this->assertSame(1, $invoice->items()->count());
        $this->assertSame('500.00', (string) InvoiceItem::first()->unit_price);
        $this->assertSame('500.00', (string) $invoice->gross_total);
    }

    public function test_a_new_invoice_is_unpaid(): void
    {
        $this->raise();

        $invoice = Payment::first();
        $this->assertSame('unpaid', $invoice->settlement());
        $this->assertSame(500.0, $invoice->balance());
    }

    public function test_a_part_payment_leaves_a_balance(): void
    {
        $this->raise();
        $invoice = Payment::first();

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => 200])
            ->assertSessionHasNoErrors();

        $invoice = $invoice->fresh(['payments']);
        $this->assertSame('part paid', $invoice->settlement());
        $this->assertSame(300.0, $invoice->balance());
    }

    public function test_paying_the_balance_settles_the_invoice(): void
    {
        $this->raise();
        $invoice = Payment::first();

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => 200]);
        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => 300]);

        $invoice = $invoice->fresh(['payments']);
        $this->assertSame('paid', $invoice->settlement());
        $this->assertSame(0.0, $invoice->balance());
        $this->assertSame(2, $invoice->payments()->count(), 'both instalments are kept, not merged');
    }

    public function test_overpayment_is_refused(): void
    {
        $this->raise();
        $invoice = Payment::first();

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => 501])
            ->assertSessionHasErrors('amount');

        $this->assertSame(0.0, $invoice->fresh('payments')->paid());
    }

    public function test_the_legacy_amount_received_column_is_kept_in_step(): void
    {
        $this->raise();
        $invoice = Payment::first();
        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => 200]);

        $this->assertSame('200.00', (string) $invoice->fresh()->amount_received);
        $this->assertSame('part paid', $invoice->fresh()->status);
    }

    public function test_discount_and_tax_apply_to_the_total(): void
    {
        $this->raise(null, ['discount' => 100, 'vat' => 50]);

        $this->assertSame('450.00', (string) Payment::first()->gross_total, '500 less 100 plus 50');
    }

    public function test_a_discount_larger_than_the_invoice_is_refused(): void
    {
        $this->raise(null, ['discount' => 600])->assertSessionHasErrors('discount');

        $this->assertSame(0, Payment::count());
    }

    public function test_an_invoice_needs_at_least_one_charge(): void
    {
        $this->raise([])->assertSessionHasErrors('items');
        $this->assertSame(0, Payment::count());
    }

    public function test_a_charge_outside_the_price_list_can_be_added(): void
    {
        $this->raise([['payment_category_id' => '', 'name' => 'Dressing pack', 'quantity' => 2, 'unit_price' => 75]])
            ->assertSessionHasNoErrors();

        $item = InvoiceItem::first();
        $this->assertSame('Dressing pack', $item->name);
        $this->assertSame('150.00', (string) $item->line_total);
    }

    public function test_the_price_does_not_move_when_the_price_list_changes(): void
    {
        $this->raise();
        $this->consult->update(['c_price' => '900']);

        $this->assertSame('500.00', (string) InvoiceItem::first()->unit_price);
    }

    public function test_an_invoice_with_money_taken_cannot_be_deleted(): void
    {
        $this->raise();
        $invoice = Payment::first();
        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => 100]);

        $this->delete('/invoices/' . $invoice->id);

        $this->assertNotNull(Payment::find($invoice->id));
    }

    public function test_outstanding_and_settled_lists_separate_correctly(): void
    {
        $this->raise();
        $invoice = Payment::first();

        $this->get('/invoices?show=outstanding')->assertSee('Amina Yousaf');
        $this->get('/invoices?show=settled')->assertSee('Nothing to show');

        $this->post('/invoices/' . $invoice->id . '/pay', ['amount' => 500]);

        $this->get('/invoices?show=settled')->assertSee('Amina Yousaf');
        $this->get('/invoices?show=outstanding')->assertSee('Nothing to show');
    }
}
