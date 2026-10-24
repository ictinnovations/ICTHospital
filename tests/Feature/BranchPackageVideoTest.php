<?php
/**
 * ICTHospital - video visits, service kinds and packages, hospital branches.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HospitalBranch;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\ServicePackageItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchPackageVideoTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $patient;
    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['firstname' => 'Head', 'lastname' => 'Office', 'login' => 'ho', 'email' => 'ho@example.test',
            'desc' => '', 'group' => 'Admin', 'password' => bcrypt('secret')]);
        $this->actingAs($this->admin);
        $this->patient = Patient::create(['name' => 'Bilal Akhtar', 'patient_id' => '000070', 'img_url' => '', 'email' => '', 'doctor' => '',
            'address' => '', 'phone' => '', 'sex' => '', 'age' => '', 'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test']);
        $this->doctor = Doctor::create(['name' => 'Dr Umair', 'img_url' => '', 'email' => '', 'address' => '', 'phone' => '',
            'department' => '', 'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '']);
    }

    private function book(array $extra = [])
    {
        return $this->post('/appointments', array_merge(['patient' => $this->patient->id, 'doctor' => $this->doctor->id,
            'date' => now()->addDay()->toDateString(), 'start' => '10:00', 'minutes' => 15, 'status' => 'scheduled'], $extra));
    }

    public function test_a_video_visit_gets_an_unguessable_room_that_survives_edits(): void
    {
        $this->book(['video' => 1])->assertSessionHasNoErrors();
        $a = Appointment::first();
        $this->assertSame('video', $a->mode);
        $this->assertMatchesRegularExpression('/^icthospital-[0-9a-f]{24}$/', $a->video_room);

        $room = $a->video_room;
        $this->put('/appointments/' . $a->id, ['patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'date' => $a->date,
            'start' => '10:30', 'minutes' => 15, 'status' => 'scheduled', 'video' => 1])->assertSessionHasNoErrors();
        $this->assertSame($room, $a->fresh()->video_room);

        $this->get('/appointments/' . $a->id)->assertOk()->assertSee('https://meet.jit.si/' . $room)->assertSee('Start video call');
    }

    public function test_an_in_person_visit_has_no_room(): void
    {
        $this->book();
        $this->assertNull(Appointment::first()->video_room);
    }

    public function test_package_contents_show_on_the_invoice(): void
    {
        $pkg = PaymentCategory::create(['category' => 'Normal delivery package', 'c_price' => '45000', 'service_kind' => 'package']);
        $room = PaymentCategory::create(['category' => 'Private room (day)', 'c_price' => '5000']);
        $this->post('/services/' . $pkg->id . '/package', ['component_id' => $room->id, 'quantity' => 2])->assertSessionHasNoErrors();
        $this->post('/services/' . $pkg->id . '/package', ['component_id' => $pkg->id])->assertSessionHasErrors('component_id');

        $this->post('/invoices', ['patient' => $this->patient->id, 'date' => now()->toDateString(), 'items' => [['payment_category_id' => $pkg->id]]]);
        $inv = Payment::first();
        $this->assertSame('45000.00', (string) $inv->gross_total);
        $this->get('/invoices/' . $inv->id)->assertSee('Includes: Private room (day) x2');
        $this->assertSame(1, ServicePackageItem::count());
    }

    public function test_service_kind_is_saved_and_shows_in_the_day_book(): void
    {
        $dialysis = PaymentCategory::create(['category' => 'Dialysis session', 'c_price' => '7000']);
        $this->put('/services/' . $dialysis->id, ['category' => 'Dialysis session', 'c_price' => 7000, 'service_kind' => 'dialysis'])->assertSessionHasNoErrors();
        $this->assertSame('dialysis', $dialysis->fresh()->service_kind);

        $this->post('/invoices', ['patient' => $this->patient->id, 'date' => now()->toDateString(), 'items' => [['payment_category_id' => $dialysis->id]]]);
        $this->get('/day-book')->assertOk()->assertSee('Dialysis: 7,000.00');
    }

    public function test_records_are_stamped_and_lists_filtered_by_branch(): void
    {
        $north = HospitalBranch::create(['name' => 'North', 'active' => true]);
        $south = HospitalBranch::create(['name' => 'South', 'active' => true]);
        $svc = PaymentCategory::create(['category' => 'Consultation', 'c_price' => '1000']);

        $this->post('/branch/switch', ['branch_id' => $north->id]);
        $this->post('/invoices', ['patient' => $this->patient->id, 'date' => now()->toDateString(), 'items' => [['payment_category_id' => $svc->id]], 'remarks' => 'north one']);
        $this->post('/branch/switch', ['branch_id' => $south->id]);
        $this->post('/invoices', ['patient' => $this->patient->id, 'date' => now()->toDateString(), 'items' => [['payment_category_id' => $svc->id]], 'remarks' => 'south one']);

        $this->assertSame([$north->id, $south->id], Payment::orderBy('id')->pluck('branch_id')->map(fn ($v) => (int) $v)->all());

        $listed = $this->get('/invoices?show=all')->viewData('invoices');
        $this->assertSame([$south->id], $listed->pluck('branch_id')->map(fn ($v) => (int) $v)->values()->all());

        $this->post('/branch/switch', ['branch_id' => '']);
        $this->assertCount(2, $this->get('/invoices?show=all')->viewData('invoices'));
    }

    public function test_a_user_tied_to_a_branch_cannot_switch(): void
    {
        $north = HospitalBranch::create(['name' => 'North', 'active' => true]);
        $south = HospitalBranch::create(['name' => 'South', 'active' => true]);
        $this->post('/hospital-branches/' . $north->id . '/users', ['users' => [$this->admin->id]])->assertSessionHasNoErrors();
        $this->actingAs($this->admin->fresh());

        $this->post('/branch/switch', ['branch_id' => $south->id]);
        $this->assertSame($north->id, \App\Support\Branch::current());
    }

    public function test_screens_render(): void
    {
        HospitalBranch::create(['name' => 'Main', 'active' => true]);
        $pkg = PaymentCategory::create(['category' => 'Health check', 'c_price' => '9000', 'service_kind' => 'package']);
        $this->get('/hospital-branches')->assertOk()->assertSee('Main');
        $this->get('/services/' . $pkg->id . '/package')->assertOk()->assertSee('Health check');
        $this->get('/services')->assertOk()->assertSee('Kind not set', false);
        $this->get('/dashboard')->assertOk()->assertSee('All branches');
    }
}
