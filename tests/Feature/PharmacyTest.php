<?php
/**
 * ICTHospital - pharmacy dispensing feature tests.
 *
 * Stock movement is the reason this module exists rather than a form that writes
 * a row, so that is what most of these check.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PharmacyPayment;
use App\Models\PharmacySaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacyTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $panadol;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Pharm', 'login' => 'pharm',
            'email' => 'pharm@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Amina Yousaf', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);

        $this->panadol = Medicine::create([
            'name' => 'Panadol', 'quantity' => 10, 's_price' => '5.00',
        ]);
    }

    private function sell(array $items, array $extra = [])
    {
        return $this->post('/pharmacy', array_merge([
            'patient' => $this->patient->id,
            'date' => '2026-10-01',
            'items' => $items,
        ], $extra));
    }

    public function test_dispensing_takes_the_quantity_out_of_stock(): void
    {
        $this->sell([['medicine_id' => $this->panadol->id, 'quantity' => 3]])
            ->assertSessionHasNoErrors();

        $this->assertSame(7, (int) $this->panadol->fresh()->quantity);
        $this->assertSame(1, PharmacySaleItem::count());
    }

    public function test_the_catalogue_price_is_used_when_none_is_given(): void
    {
        $this->sell([['medicine_id' => $this->panadol->id, 'quantity' => 2]]);

        $item = PharmacySaleItem::first();
        $this->assertSame('5.00', (string) $item->unit_price);
        $this->assertSame('10.00', (string) $item->line_total);
    }

    public function test_the_sale_price_does_not_move_when_the_catalogue_changes(): void
    {
        $this->sell([['medicine_id' => $this->panadol->id, 'quantity' => 1]]);
        $this->panadol->update(['s_price' => '9.99']);

        $this->assertSame('5.00', (string) PharmacySaleItem::first()->unit_price);
    }

    public function test_stock_cannot_go_negative(): void
    {
        $this->sell([['medicine_id' => $this->panadol->id, 'quantity' => 11]])
            ->assertSessionHasErrors('items');

        $this->assertSame(10, (int) $this->panadol->fresh()->quantity);
        $this->assertSame(0, PharmacyPayment::count());
    }

    public function test_the_same_drug_twice_on_one_sale_is_counted_together(): void
    {
        // Six and six each pass on their own, but twelve is more than the ten in
        // stock, so the sale has to be refused.
        $this->sell([
            ['medicine_id' => $this->panadol->id, 'quantity' => 6],
            ['medicine_id' => $this->panadol->id, 'quantity' => 6],
        ])->assertSessionHasErrors('items');

        $this->assertSame(10, (int) $this->panadol->fresh()->quantity);
    }

    public function test_reversing_a_sale_puts_the_stock_back(): void
    {
        $this->sell([['medicine_id' => $this->panadol->id, 'quantity' => 4]]);
        $this->assertSame(6, (int) $this->panadol->fresh()->quantity);

        $this->delete('/pharmacy/' . PharmacyPayment::first()->id);

        $this->assertSame(10, (int) $this->panadol->fresh()->quantity);
        $this->assertSame(0, PharmacySaleItem::count());
    }

    public function test_totals_apply_discount_and_tax(): void
    {
        $this->sell(
            [['medicine_id' => $this->panadol->id, 'quantity' => 4]],
            ['discount' => 2, 'vat' => 1]
        );

        $sale = PharmacyPayment::first();
        $this->assertSame('20.00', (string) $sale->amount);
        $this->assertSame('19.00', (string) $sale->gross_total, '20 less 2 discount plus 1 tax');
    }

    public function test_an_item_outside_the_catalogue_can_be_sold(): void
    {
        $this->sell([['medicine_id' => '', 'name' => 'Imported dressing', 'quantity' => 1, 'unit_price' => 3.5]])
            ->assertSessionHasNoErrors();

        $item = PharmacySaleItem::first();
        $this->assertSame('Imported dressing', $item->name);
        $this->assertNull($item->medicine_id);
    }

    public function test_a_sale_needs_at_least_one_item(): void
    {
        $this->sell([])->assertSessionHasErrors('items');
        $this->assertSame(0, PharmacyPayment::count());
    }

    public function test_a_counter_sale_needs_no_patient(): void
    {
        $this->post('/pharmacy', [
            'date' => '2026-10-01',
            'items' => [['medicine_id' => $this->panadol->id, 'quantity' => 1]],
        ])->assertSessionHasNoErrors();

        $this->assertNull(PharmacyPayment::first()->patient);
    }
}
