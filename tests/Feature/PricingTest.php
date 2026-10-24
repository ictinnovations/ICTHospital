<?php
/**
 * ICTHospital - patient types, price lists per type and the fuller registration form.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientType;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\ServicePrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingTest extends TestCase
{
    use RefreshDatabase;

    private $general;
    private $panel;
    private $xray;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Billing', 'login' => 'billing',
            'email' => 'billing@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->general = PatientType::where('kind', 'general')->first();
        $this->panel = PatientType::create(['name' => 'State Life panel', 'kind' => 'panel', 'company' => 'State Life', 'active' => true]);
        $this->xray = PaymentCategory::create(['category' => 'X-ray chest', 'c_price' => '1000']);
    }

    private function patient(array $extra = [])
    {
        return Patient::create(array_merge([
            'name' => 'Bilal Ahmed', 'patient_id' => '000010', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ], $extra));
    }

    public function test_a_clean_install_has_a_general_type(): void
    {
        $this->assertNotNull($this->general);
        $this->assertSame($this->general->id, PatientType::defaultId());
    }

    public function test_registration_stores_the_new_fields_and_formats_the_cnic(): void
    {
        $this->post('/patients', [
            'name' => 'Sana Khalid', 'cnic' => '3520212345671', 'mlc' => '1', 'guardian_relation' => 'Father',
            'guardian_mrn' => '000004', 'old_mrn' => 'OLD-77', 'city' => 'Multan', 'patient_type_id' => $this->panel->id,
        ])->assertSessionHasNoErrors();

        $p = Patient::where('name', 'Sana Khalid')->first();
        $this->assertSame('35202-1234567-1', $p->cnic);
        $this->assertTrue((bool) $p->mlc);
        $this->assertSame('Multan', $p->city);
        $this->assertSame($this->panel->id, (int) $p->patient_type_id);
    }

    public function test_a_patient_registered_without_a_type_is_general(): void
    {
        $this->post('/patients', ['name' => 'Walk In'])->assertSessionHasNoErrors();
        $this->assertSame($this->general->id, (int) Patient::where('name', 'Walk In')->value('patient_type_id'));
    }

    public function test_a_malformed_cnic_is_refused(): void
    {
        $this->post('/patients', ['name' => 'X', 'cnic' => '12345'])->assertSessionHasErrors('cnic');
    }

    public function test_patients_can_be_found_by_cnic_or_old_mr_number(): void
    {
        $this->patient(['cnic' => '35202-1234567-1', 'old_mrn' => 'OLD-77']);
        $this->get('/patients?q=1234567')->assertSee('Bilal Ahmed');
        $this->get('/patients?q=OLD-77')->assertSee('Bilal Ahmed');
    }

    public function test_the_price_list_is_saved_per_type_and_setting(): void
    {
        $this->post('/pricing', ['type' => $this->panel->id, 'opd' => [$this->xray->id => 1200], 'ipd' => [$this->xray->id => 1500]])
            ->assertSessionHasNoErrors();

        $row = ServicePrice::first();
        $this->assertSame('1200.00', (string) $row->opd_price);
        $this->assertSame('1500.00', (string) $row->ipd_price);
    }

    public function test_an_invoice_is_priced_from_the_patients_type_and_setting(): void
    {
        ServicePrice::create(['payment_category_id' => $this->xray->id, 'patient_type_id' => $this->panel->id, 'opd_price' => 1200, 'ipd_price' => 1500]);
        $p = $this->patient(['patient_type_id' => $this->panel->id]);

        $this->post('/invoices', ['patient' => $p->id, 'date' => '2026-10-24', 'care_setting' => 'ipd',
            'items' => [['payment_category_id' => $this->xray->id, 'quantity' => 1]]])->assertSessionHasNoErrors();

        $inv = Payment::first();
        $this->assertSame('1500.00', (string) $inv->gross_total);
        $this->assertSame($this->panel->id, (int) $inv->patient_type_id);
        $this->assertSame('ipd', $inv->care_setting);
    }

    public function test_a_type_without_a_price_pays_the_list_price(): void
    {
        $p = $this->patient(['patient_type_id' => $this->panel->id]);
        $this->post('/invoices', ['patient' => $p->id, 'date' => '2026-10-24',
            'items' => [['payment_category_id' => $this->xray->id, 'quantity' => 2]]]);

        $this->assertSame('2000.00', (string) Payment::first()->gross_total);
    }

    public function test_changing_prices_later_leaves_old_invoices_alone(): void
    {
        $p = $this->patient(['patient_type_id' => $this->panel->id]);
        $this->post('/invoices', ['patient' => $p->id, 'date' => '2026-10-24',
            'items' => [['payment_category_id' => $this->xray->id, 'quantity' => 1]]]);
        $this->post('/pricing/adjust', ['type' => $this->panel->id, 'percent' => 50, 'setting' => 'both']);

        $this->assertSame('1000.00', (string) Payment::first()->gross_total);
    }

    public function test_adjust_starts_from_the_list_price_when_a_type_has_none(): void
    {
        $this->post('/pricing/adjust', ['type' => $this->panel->id, 'percent' => 10, 'setting' => 'opd'])->assertSessionHasNoErrors();

        $row = ServicePrice::where('patient_type_id', $this->panel->id)->first();
        $this->assertSame('1100.00', (string) $row->opd_price);
        $this->assertNull($row->ipd_price);
    }

    public function test_copy_replaces_the_target_list(): void
    {
        ServicePrice::create(['payment_category_id' => $this->xray->id, 'patient_type_id' => $this->general->id, 'opd_price' => 900]);
        $this->post('/pricing/copy', ['from' => $this->general->id, 'to' => $this->panel->id])->assertSessionHasNoErrors();

        $this->assertSame('900.00', (string) ServicePrice::where('patient_type_id', $this->panel->id)->value('opd_price'));
    }

    public function test_the_price_list_downloads_as_csv(): void
    {
        $res = $this->get('/pricing/export?type=' . $this->panel->id);
        $res->assertOk();
        $this->assertStringContainsString('X-ray chest', $res->getContent());
        $this->assertStringContainsString('text/csv', $res->headers->get('Content-Type'));
    }

    public function test_a_type_in_use_is_switched_off_not_deleted(): void
    {
        $this->patient(['patient_type_id' => $this->panel->id]);
        $this->delete('/patient-types/' . $this->panel->id);

        $this->assertFalse((bool) $this->panel->fresh()->active);
    }

    public function test_health_card_and_wristband_print_with_a_barcode(): void
    {
        $p = $this->patient(['mlc' => true]);
        $this->get('/patients/' . $p->id . '/card')->assertOk()->assertSee('000010')->assertSee('data:image/png;base64', false);
        $this->get('/patients/' . $p->id . '/wristband')->assertOk()->assertSee('MLC');
    }

    public function test_pricing_screens_render(): void
    {
        $this->get('/pricing?type=' . $this->panel->id)->assertOk()->assertSee('X-ray chest');
        $this->get('/patient-types')->assertOk()->assertSee('State Life panel');
        $this->get('/patients/create')->assertOk()->assertSee('Medico-legal case');
        $this->get('/invoices/create')->assertOk()->assertSee('Care setting');
    }
}
