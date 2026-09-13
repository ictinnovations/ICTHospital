<?php
/**
 * ICTHospital - medical history and payment gateway feature tests.
 *
 * Two things carry most of the weight here. A history entry keeps the patient details
 * as they were when it was written, so an old note still reads correctly after someone
 * moves house. And a gateway secret is never rendered back into the page, because a
 * settings screen that prints an API password puts it in every browser cache.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalHistoryTest extends TestCase
{
    use RefreshDatabase;

    private $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Clinician', 'login' => 'historyuser',
            'email' => 'history@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Imran Shah', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '12 Old Street', 'phone' => '03001234567', 'sex' => '',
            'age' => '', 'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
    }

    private function add(array $overrides = [])
    {
        return $this->post('/history', array_merge([
            'patient_id' => $this->patient->id,
            'title' => 'Penicillin allergy',
            'description' => 'Rash and swelling after amoxicillin in 2024. Avoid all penicillins.',
            'date' => '2026-09-01',
        ], $overrides));
    }

    public function test_an_entry_is_recorded_against_a_patient(): void
    {
        $this->add()->assertSessionHasNoErrors();

        $entry = MedicalHistory::first();
        $this->assertNotNull($entry);
        $this->assertSame('Penicillin allergy', $entry->title);
        $this->assertSame($this->patient->id, (int) $entry->patient_id);
    }

    public function test_a_title_and_notes_are_both_required(): void
    {
        $this->add(['title' => ''])->assertSessionHasErrors('title');
        $this->add(['description' => ''])->assertSessionHasErrors('description');

        $this->assertSame(0, MedicalHistory::count());
    }

    public function test_an_entry_needs_a_real_patient(): void
    {
        $this->add(['patient_id' => 9999])->assertSessionHasErrors('patient_id');
    }

    public function test_the_date_defaults_to_today_when_left_blank(): void
    {
        $this->add(['date' => ''])->assertSessionHasNoErrors();

        $this->assertSame(now()->toDateString(), (string) MedicalHistory::first()->date);
    }

    /**
     * The reason the patient details are copied onto the entry rather than only
     * joined: an old note has to keep reading correctly.
     */
    public function test_an_old_entry_keeps_the_details_as_they_were(): void
    {
        $this->add();
        $entry = MedicalHistory::first();

        $this->assertSame('12 Old Street', $entry->patient_address);
        $this->assertSame('03001234567', $entry->patient_phone);

        $this->patient->update(['address' => '99 New Road', 'phone' => '03119999999']);

        $this->assertSame('12 Old Street', $entry->fresh()->patient_address);
        $this->assertSame('03001234567', $entry->fresh()->patient_phone);
    }

    public function test_the_list_filters_by_patient(): void
    {
        $other = Patient::create([
            'name' => 'Other Person', 'patient_id' => '000002', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);

        // Written directly rather than through the form, because store() flashes
        // 'Added "Diabetes" to the history' into the session and the next page
        // renders that alert, which would make assertDontSee fail on the message
        // rather than on a row that leaked through the filter.
        MedicalHistory::create([
            'patient_id' => $this->patient->id, 'title' => 'Asthma',
            'description' => 'Inhaler as needed.', 'date' => '2026-09-01',
        ]);
        MedicalHistory::create([
            'patient_id' => $other->id, 'title' => 'Diabetes',
            'description' => 'Type 2, diet controlled.', 'date' => '2026-09-01',
        ]);

        $this->get('/history?patient=' . $this->patient->id)
            ->assertOk()
            ->assertSee('Asthma')
            ->assertDontSee('Diabetes');

        $this->get('/history')
            ->assertOk()
            ->assertSee('Asthma')
            ->assertSee('Diabetes');
    }

    public function test_an_entry_can_be_edited_and_removed(): void
    {
        $this->add();
        $entry = MedicalHistory::first();

        $this->put('/history/' . $entry->id, [
            'patient_id' => $this->patient->id,
            'title' => 'Penicillin and sulfa allergy',
            'description' => 'Updated after a reaction to co-trimoxazole.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Penicillin and sulfa allergy', $entry->fresh()->title);

        $this->delete('/history/' . $entry->id);
        $this->assertNull(MedicalHistory::find($entry->id));
    }

    /* ---------------- payment gateways ---------------- */

    public function test_a_gateway_is_added_disabled(): void
    {
        $this->post('/gateways', ['name' => 'PayPal', 'APIUsername' => 'merchant-api'])
            ->assertSessionHasNoErrors();

        $gateway = PaymentGateway::first();
        $this->assertNotNull($gateway);
        $this->assertSame('disabled', $gateway->status);
    }

    public function test_a_secret_is_never_rendered_back_into_the_page(): void
    {
        $this->post('/gateways', [
            'name' => 'PayPal',
            'APIPassword' => 'super-secret-value',
            'salt' => 'another-secret',
        ]);

        $this->get('/gateways')
            ->assertOk()
            ->assertSee('PayPal')
            ->assertDontSee('super-secret-value')
            ->assertDontSee('another-secret');
    }

    public function test_a_blank_secret_keeps_the_stored_one(): void
    {
        $this->post('/gateways', ['name' => 'PayPal', 'APIPassword' => 'original-password']);
        $gateway = PaymentGateway::first();

        $this->put('/gateways/' . $gateway->id, ['name' => 'PayPal Live', 'APIPassword' => ''])
            ->assertSessionHasNoErrors();

        $this->assertSame('PayPal Live', $gateway->fresh()->name);
        $this->assertSame('original-password', $gateway->fresh()->APIPassword);
    }

    public function test_a_typed_secret_replaces_the_stored_one(): void
    {
        $this->post('/gateways', ['name' => 'PayPal', 'APIPassword' => 'original-password']);
        $gateway = PaymentGateway::first();

        $this->put('/gateways/' . $gateway->id, ['name' => 'PayPal', 'APIPassword' => 'rotated-password']);

        $this->assertSame('rotated-password', $gateway->fresh()->APIPassword);
    }

    public function test_editing_a_gateway_does_not_switch_it_off(): void
    {
        $this->post('/gateways', ['name' => 'PayPal']);
        $gateway = PaymentGateway::first();
        $this->post('/gateways/' . $gateway->id . '/enable');

        $this->assertSame('enabled', $gateway->fresh()->status);

        $this->put('/gateways/' . $gateway->id, ['name' => 'PayPal Live']);

        $this->assertSame('enabled', $gateway->fresh()->status);
    }

    public function test_making_one_gateway_active_disables_the_others(): void
    {
        $this->post('/gateways', ['name' => 'PayPal']);
        $this->post('/gateways', ['name' => 'Stripe']);

        $paypal = PaymentGateway::where('name', 'PayPal')->first();
        $stripe = PaymentGateway::where('name', 'Stripe')->first();

        $this->post('/gateways/' . $paypal->id . '/enable');
        $this->assertSame('enabled', $paypal->fresh()->status);

        $this->post('/gateways/' . $stripe->id . '/enable');

        $this->assertSame('enabled', $stripe->fresh()->status);
        $this->assertSame('disabled', $paypal->fresh()->status);
    }

    public function test_two_gateways_cannot_share_a_name(): void
    {
        $this->post('/gateways', ['name' => 'PayPal']);
        $this->post('/gateways', ['name' => 'PayPal'])->assertSessionHasErrors('name');

        $this->assertSame(1, PaymentGateway::count());
    }

    public function test_the_screen_says_no_card_is_charged_yet(): void
    {
        $this->get('/gateways')->assertOk()->assertSee('Nothing in this release charges a card');
    }
}
