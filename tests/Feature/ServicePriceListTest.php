<?php
/**
 * ICTHospital - service and price list feature tests.
 *
 * The one that matters most is the last: editing a price must not rewrite an
 * invoice that was already raised at the old price. Every catalogue in this
 * system carries the same test, because that is the failure the child tables
 * were introduced to prevent.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\PaymentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePriceListTest extends TestCase
{
    use RefreshDatabase;

    private $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Accounts', 'login' => 'priceadmin',
            'email' => 'prices@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Bilal Ahmed', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
    }

    public function test_a_service_can_be_added_with_a_price(): void
    {
        $this->post('/services', [
            'category' => 'Consultation, general',
            'type' => 'Consultation',
            'c_price' => '1500',
        ])->assertSessionHasNoErrors();

        $service = PaymentCategory::first();
        $this->assertNotNull($service);
        $this->assertSame('1500.00', $service->c_price);
    }

    public function test_a_service_can_be_added_without_a_price(): void
    {
        $this->post('/services', ['category' => 'Dressing'])->assertSessionHasNoErrors();

        $this->assertNull(PaymentCategory::first()->c_price);
    }

    public function test_the_price_list_refuses_a_duplicate_service(): void
    {
        $this->post('/services', ['category' => 'X-ray']);
        $this->post('/services', ['category' => 'X-ray'])->assertSessionHasErrors('category');

        $this->assertSame(1, PaymentCategory::count());
    }

    public function test_a_negative_price_is_rejected(): void
    {
        $this->post('/services', ['category' => 'Refundable', 'c_price' => '-10'])
            ->assertSessionHasErrors('c_price');
    }

    public function test_a_price_can_be_corrected_in_place(): void
    {
        $this->post('/services', ['category' => 'Ward, private', 'c_price' => '4000']);
        $service = PaymentCategory::first();

        $this->put('/services/' . $service->id, [
            'category' => 'Ward, private',
            'c_price' => '4500',
        ])->assertSessionHasNoErrors();

        $this->assertSame('4500.00', $service->fresh()->c_price);
    }

    public function test_an_invoiced_service_is_not_deleted(): void
    {
        $service = PaymentCategory::create(['category' => 'Theatre', 'c_price' => '20000.00']);
        InvoiceItem::create([
            'payment_id' => 1, 'payment_category_id' => $service->id, 'name' => 'Theatre',
            'quantity' => 1, 'unit_price' => '20000.00', 'line_total' => '20000.00',
        ]);

        $this->delete('/services/' . $service->id);

        $this->assertNotNull(PaymentCategory::find($service->id));
    }

    public function test_an_unused_service_is_deleted(): void
    {
        $service = PaymentCategory::create(['category' => 'Typo entry']);

        $this->delete('/services/' . $service->id);

        $this->assertNull(PaymentCategory::find($service->id));
    }

    /**
     * The reason the whole module is shaped this way.
     */
    public function test_raising_the_price_later_does_not_change_an_existing_invoice(): void
    {
        $service = PaymentCategory::create(['category' => 'Consultation', 'c_price' => '1000.00']);

        $this->post('/invoices', [
            'patient' => $this->patient->id,
            'date' => '2026-10-01',
            'items' => [['payment_category_id' => $service->id, 'quantity' => 1]],
        ])->assertSessionHasNoErrors();

        $line = InvoiceItem::first();
        $this->assertSame('1000.00', $line->unit_price);

        $this->put('/services/' . $service->id, ['category' => 'Consultation', 'c_price' => '1800']);

        $this->assertSame('1800.00', $service->fresh()->c_price);
        $this->assertSame('1000.00', $line->fresh()->unit_price);
        $this->assertSame('1000.00', $line->fresh()->line_total);
    }
}
