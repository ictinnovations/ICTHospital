<?php
/**
 * ICTHospital - shift cash close, day book and final settlement.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\InvoicePayment;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\ShiftClose;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashTest extends TestCase
{
    use RefreshDatabase;

    private $cashier;
    private $patient;
    private $consult;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashier = User::create([
            'firstname' => 'Desk', 'lastname' => 'One', 'login' => 'desk1', 'email' => 'd1@example.test',
            'desc' => '', 'group' => 'Admin', 'password' => bcrypt('secret'),
        ]);
        $this->actingAs($this->cashier);

        $this->patient = Patient::create([
            'name' => 'Zainab Fatima', 'patient_id' => '000030', 'img_url' => '', 'email' => '', 'doctor' => '',
            'address' => '', 'phone' => '', 'sex' => '', 'age' => '', 'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
        $this->consult = PaymentCategory::create(['category' => 'Consultation', 'c_price' => '1000', 'department' => 'General OPD']);
    }

    private function invoice($qty = 1, array $extra = [])
    {
        $this->post('/invoices', array_merge(['patient' => $this->patient->id, 'date' => now()->toDateString(),
            'items' => [['payment_category_id' => $this->consult->id, 'quantity' => $qty]]], $extra))->assertSessionHasNoErrors();

        return Payment::latest('id')->first();
    }

    public function test_a_payment_records_who_took_it(): void
    {
        $inv = $this->invoice();
        $this->post('/invoices/' . $inv->id . '/pay', ['amount' => 1000, 'method' => 'Cash']);

        $this->assertSame($this->cashier->id, (int) InvoicePayment::first()->received_by);
    }

    public function test_closing_a_shift_totals_by_mode_and_locks_the_payments(): void
    {
        $a = $this->invoice();
        $b = $this->invoice(2);
        $this->post('/invoices/' . $a->id . '/pay', ['amount' => 1000, 'method' => 'Cash']);
        $this->post('/invoices/' . $b->id . '/pay', ['amount' => 1500, 'method' => 'Card']);
        $this->post('/invoices/' . $b->id . '/pay', ['amount' => 500, 'method' => 'Bank transfer']);

        $this->post('/cash-close', ['counted_cash' => 950, 'comment' => 'Short 50'])->assertSessionHasNoErrors();

        $close = ShiftClose::first();
        $this->assertSame('1000.00', (string) $close->cash);
        $this->assertSame('1500.00', (string) $close->card);
        $this->assertSame('500.00', (string) $close->bank_transfer);
        $this->assertSame('3000.00', (string) $close->total);
        $this->assertSame('-50.00', (string) $close->difference);
        $this->assertSame(0, InvoicePayment::whereNull('shift_close_id')->count());
    }

    public function test_a_payment_is_never_in_two_closes(): void
    {
        $a = $this->invoice();
        $this->post('/invoices/' . $a->id . '/pay', ['amount' => 1000, 'method' => 'Cash']);
        $this->post('/cash-close', []);
        $this->post('/cash-close', [])->assertSessionHasErrors('counted_cash');

        $this->assertSame(1, ShiftClose::count());
    }

    public function test_a_cashier_only_closes_their_own_payments(): void
    {
        $a = $this->invoice();
        $this->post('/invoices/' . $a->id . '/pay', ['amount' => 400, 'method' => 'Cash']);

        $other = User::create(['firstname' => 'Desk', 'lastname' => 'Two', 'login' => 'desk2', 'email' => 'd2@example.test',
            'desc' => '', 'group' => 'Receptionist', 'password' => bcrypt('secret')]);
        \Illuminate\Support\Facades\DB::table('permission')->where('permission_group', 'receptionist')
            ->whereIn('permission_name', ['cash_close', 'payment_add', 'invoice_view'])->update(['permission_type' => 'yes']);
        \App\Support\Permissions::flush();
        $this->actingAs($other);
        $this->post('/invoices/' . $a->id . '/pay', ['amount' => 600, 'method' => 'Cash']);
        $this->post('/cash-close', ['user_id' => $this->cashier->id]);

        $close = ShiftClose::first();
        $this->assertSame($other->id, (int) $close->user_id);
        $this->assertSame('600.00', (string) $close->total);
    }

    public function test_day_book_splits_by_department_and_patient_group(): void
    {
        $panel = PatientType::create(['name' => 'Panel A', 'kind' => 'panel', 'active' => true]);
        $this->invoice();
        $this->invoice(1, ['patient_type_id' => $panel->id]);

        $res = $this->get('/day-book');
        $res->assertOk()->assertSee('General OPD')->assertSee('Panel');
        $csv = $this->get('/day-book?export=csv');
        $this->assertStringContainsString('"Panel"', $csv->getContent());
        $this->assertStringContainsString('"Private"', $csv->getContent());
    }

    public function test_settlement_pays_oldest_invoices_first(): void
    {
        $old = $this->invoice();
        $new = $this->invoice(2);

        $this->post('/settlement/' . $this->patient->id, ['amount' => 1500, 'method' => 'Cash'])->assertSessionHasNoErrors();

        $this->assertSame(0.0, $old->fresh()->balance());
        $this->assertSame(1500.0, $new->fresh()->balance());
    }

    public function test_settlement_refuses_more_than_is_owed(): void
    {
        $this->invoice();
        $this->post('/settlement/' . $this->patient->id, ['amount' => 5000, 'method' => 'Cash'])->assertSessionHasErrors('amount');
    }

    public function test_a_write_off_needs_a_reason_and_is_not_money_collected(): void
    {
        $inv = $this->invoice();
        $this->post('/settlement/' . $this->patient->id, ['amount' => 800, 'method' => 'Cash', 'write_off' => 200])->assertSessionHasErrors('write_off_reason');

        $this->post('/settlement/' . $this->patient->id, ['amount' => 800, 'method' => 'Cash', 'write_off' => 200, 'write_off_reason' => 'Poor patient'])
            ->assertSessionHasNoErrors();
        $this->assertSame(0.0, $inv->fresh()->balance());

        $this->post('/cash-close', []);
        $close = ShiftClose::first();
        $this->assertSame('800.00', (string) $close->total);
    }

    public function test_screens_render(): void
    {
        $inv = $this->invoice();
        $this->post('/invoices/' . $inv->id . '/pay', ['amount' => 1000, 'method' => 'Cash']);
        $this->get('/cash-close')->assertOk()->assertSee('Zainab Fatima');
        $this->post('/cash-close', []);
        $this->get('/cash-close/' . ShiftClose::first()->id)->assertOk()->assertSee('Total collected');
        $this->get('/settlement?patient=' . $this->patient->id)->assertOk()->assertSee('Receivable');
    }
}
