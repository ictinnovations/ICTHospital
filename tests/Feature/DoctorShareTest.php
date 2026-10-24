<?php
/**
 * ICTHospital - doctor share: rule matching, the base after discount, referral
 * shares, approval only once paid, payout into the ledger.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorShare;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\SharePolicy;
use App\Models\ShareRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DoctorShareTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $consult;
    private $xray;
    private $doctor;
    private $referrer;
    private $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Accounts', 'login' => 'accounts',
            'email' => 'acc@example.test', 'desc' => '', 'group' => 'Admin', 'password' => bcrypt('secret'),
        ]));

        $this->patient = Patient::create([
            'name' => 'Hira Shah', 'patient_id' => '000020', 'img_url' => '', 'email' => '', 'doctor' => '',
            'address' => '', 'phone' => '', 'sex' => '', 'age' => '', 'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
        $this->consult = PaymentCategory::create(['category' => 'Consultation', 'c_price' => '1000', 'department' => 'General OPD']);
        $this->xray = PaymentCategory::create(['category' => 'X-ray', 'c_price' => '2000', 'department' => 'Radiology']);

        $this->policy = SharePolicy::create(['name' => 'OPD 70', 'active' => true]);
        ShareRule::create(['share_policy_id' => $this->policy->id, 'doctor_percent' => 50, 'referral_percent' => 10]);
        ShareRule::create(['share_policy_id' => $this->policy->id, 'department' => 'General OPD', 'doctor_percent' => 70]);

        $this->doctor = $this->doctor('Dr Sana');
        $this->referrer = $this->doctor('Dr Imran');
    }

    private function doctor($name)
    {
        return Doctor::create([
            'name' => $name, 'img_url' => '', 'email' => '', 'address' => '', 'phone' => '', 'department' => '',
            'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '', 'share_policy_id' => $this->policy->id,
        ]);
    }

    private function raise(array $items, array $extra = [])
    {
        $this->post('/invoices', array_merge([
            'patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'date' => now()->toDateString(), 'items' => $items,
        ], $extra))->assertSessionHasNoErrors();

        return Payment::latest('id')->first();
    }

    public function test_the_most_specific_rule_wins(): void
    {
        $this->raise([['payment_category_id' => $this->consult->id], ['payment_category_id' => $this->xray->id]]);

        $shares = DoctorShare::orderBy('id')->get();
        $this->assertSame('700.00', (string) $shares[0]->amount); // department rule, 70%
        $this->assertSame('1000.00', (string) $shares[1]->amount); // catch-all, 50%
    }

    public function test_a_service_rule_beats_a_department_rule(): void
    {
        ShareRule::create(['share_policy_id' => $this->policy->id, 'payment_category_id' => $this->consult->id, 'doctor_percent' => 80]);
        $this->raise([['payment_category_id' => $this->consult->id]]);

        $this->assertSame('800.00', (string) DoctorShare::first()->amount);
    }

    public function test_a_patient_type_rule_applies_only_to_that_type(): void
    {
        $panel = PatientType::create(['name' => 'Panel A', 'kind' => 'panel', 'active' => true]);
        ShareRule::create(['share_policy_id' => $this->policy->id, 'department' => 'General OPD', 'patient_type_id' => $panel->id, 'doctor_percent' => 100]);

        $this->raise([['payment_category_id' => $this->consult->id]], ['patient_type_id' => $panel->id]);
        $this->assertSame('1000.00', (string) DoctorShare::first()->amount);

        $this->raise([['payment_category_id' => $this->consult->id]]);
        $this->assertSame('700.00', (string) DoctorShare::latest('id')->first()->amount);
    }

    public function test_the_share_is_on_the_line_after_its_part_of_the_discount(): void
    {
        // 3000 of lines, 300 discount: consultation 1000 carries 100 of it.
        $this->raise([['payment_category_id' => $this->consult->id], ['payment_category_id' => $this->xray->id]], ['discount' => 300]);

        $consultShare = DoctorShare::orderBy('id')->first();
        $this->assertSame('900.00', (string) $consultShare->base_amount);
        $this->assertSame('630.00', (string) $consultShare->amount);
    }

    public function test_a_referring_doctor_gets_the_referral_percent(): void
    {
        $this->raise([['payment_category_id' => $this->xray->id]], ['referring_doctor' => $this->referrer->id]);

        $ref = DoctorShare::where('role', 'referring')->first();
        $this->assertSame($this->referrer->id, (int) $ref->doctor_id);
        $this->assertSame('200.00', (string) $ref->amount);
    }

    public function test_a_line_can_name_its_own_doctor(): void
    {
        $other = $this->doctor('Dr Waqas');
        $this->raise([['payment_category_id' => $this->xray->id, 'doctor_id' => $other->id]]);

        $this->assertSame($other->id, (int) DoctorShare::first()->doctor_id);
    }

    public function test_a_doctor_without_a_policy_earns_no_share(): void
    {
        $this->doctor->update(['share_policy_id' => null]);
        $this->raise([['payment_category_id' => $this->consult->id]]);

        $this->assertSame(0, DoctorShare::count());
    }

    public function test_shares_cannot_be_approved_until_the_invoice_is_paid(): void
    {
        $inv = $this->raise([['payment_category_id' => $this->consult->id]]);
        $share = DoctorShare::first();

        $this->post('/doctor-share/approve', ['ids' => [$share->id]]);
        $this->assertSame('pending', $share->fresh()->status);

        $this->post('/invoices/' . $inv->id . '/pay', ['amount' => 1000, 'method' => 'Cash']);
        $this->post('/doctor-share/approve', ['ids' => [$share->id]]);
        $this->assertSame('approved', $share->fresh()->status);
    }

    public function test_paying_writes_one_ledger_expense_per_doctor(): void
    {
        $inv = $this->raise([['payment_category_id' => $this->consult->id], ['payment_category_id' => $this->xray->id]]);
        $this->post('/invoices/' . $inv->id . '/pay', ['amount' => 3000, 'method' => 'Cash']);
        $ids = DoctorShare::pluck('id')->all();
        $this->post('/doctor-share/approve', ['ids' => $ids]);
        $this->post('/doctor-share/pay', ['ids' => $ids, 'reference' => 'OCT-PAYOUT']);

        $this->assertSame(2, DoctorShare::where('status', 'paid')->where('payout_ref', 'OCT-PAYOUT')->count());
        $row = DB::table('accounting')->where('name', 'Doctor share')->get();
        $this->assertCount(1, $row);
        $this->assertEquals(1700, (float) $row[0]->amount);
    }

    public function test_an_invoice_with_an_approved_share_cannot_be_deleted(): void
    {
        $inv = $this->raise([['payment_category_id' => $this->consult->id]]);
        DoctorShare::query()->update(['status' => 'approved']);

        $this->delete('/invoices/' . $inv->id);
        $this->assertNotNull(Payment::find($inv->id));
    }

    public function test_editing_a_rule_does_not_change_shares_already_worked_out(): void
    {
        $this->raise([['payment_category_id' => $this->consult->id]]);
        ShareRule::where('department', 'General OPD')->update(['doctor_percent' => 10]);

        $this->assertSame('700.00', (string) DoctorShare::first()->amount);
    }

    public function test_policy_screens_and_report_render(): void
    {
        $this->raise([['payment_category_id' => $this->consult->id]]);
        $this->get('/doctor-share/policies')->assertOk()->assertSee('OPD 70');
        $this->get('/doctor-share')->assertOk()->assertSee('Dr Sana')->assertSee('700.00');
        $this->get('/invoices/' . Payment::first()->id)->assertOk()->assertSee('Doctor shares');
    }

    public function test_assigning_doctors_to_a_policy(): void
    {
        $other = SharePolicy::create(['name' => 'Visiting', 'active' => true]);
        $this->post('/doctor-share/policies/' . $other->id . '/assign', ['doctors' => [$this->referrer->id]]);

        $this->assertSame($other->id, (int) $this->referrer->fresh()->share_policy_id);
        $this->assertSame($this->policy->id, (int) $this->doctor->fresh()->share_policy_id);
    }
}
