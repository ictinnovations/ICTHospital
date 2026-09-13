<?php
/**
 * ICTHospital - staff and department feature tests.
 *
 * One controller drives five tables, so the tests run the same checks across all of
 * them rather than trusting that what works for nurses works for accountants. The
 * tables differ: only nurse has a z column, and receptionist and accountant have no y.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Admin', 'login' => 'staffadmin',
            'email' => 'staff@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));
    }

    public static function staffTypes(): array
    {
        return [
            'nurse' => ['nurse'],
            'pharmacist' => ['pharmacist'],
            'laboratorist' => ['laboratorist'],
            'receptionist' => ['receptionist'],
            'accountant' => ['accountant'],
        ];
    }

    #[DataProvider('staffTypes')]
    public function test_every_staff_type_accepts_a_name_only_record($type): void
    {
        $this->post('/staff/' . $type, ['name' => 'Sana Riaz'])->assertSessionHasNoErrors();

        $model = config('hospital_staff.' . $type . '.model');
        $this->assertSame(1, $model::count(), $type . ' did not save');
        $this->assertSame('Sana Riaz', $model::first()->name);
    }

    #[DataProvider('staffTypes')]
    public function test_every_staff_type_lists_and_edits($type): void
    {
        $model = config('hospital_staff.' . $type . '.model');
        $this->post('/staff/' . $type, ['name' => 'First Name', 'phone' => '0300111']);
        $person = $model::first();

        $this->get('/staff/' . $type)->assertOk()->assertSee('First Name');

        $this->put('/staff/' . $type . '/' . $person->id, ['name' => 'Changed Name'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Changed Name', $person->fresh()->name);

        $this->delete('/staff/' . $type . '/' . $person->id);
        $this->assertNull($model::find($person->id));
    }

    public function test_an_unknown_staff_type_is_a_404_not_a_guessed_table(): void
    {
        $this->get('/staff/surgeon')->assertNotFound();
        $this->get('/staff/users')->assertNotFound();
    }

    public function test_the_name_is_required(): void
    {
        $this->post('/staff/nurse', ['name' => ''])->assertSessionHasErrors('name');
        $this->assertSame(0, Nurse::count());
    }

    public function test_two_staff_of_one_type_cannot_share_an_email(): void
    {
        $this->post('/staff/nurse', ['name' => 'One', 'email' => 'same@example.test']);
        $this->post('/staff/nurse', ['name' => 'Two', 'email' => 'same@example.test'])
            ->assertSessionHasErrors('email');

        $this->assertSame(1, Nurse::count());
    }

    public function test_the_same_email_may_appear_under_two_different_types(): void
    {
        $this->post('/staff/nurse', ['name' => 'One', 'email' => 'shared@example.test'])
            ->assertSessionHasNoErrors();
        $this->post('/staff/pharmacist', ['name' => 'One', 'email' => 'shared@example.test'])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Nurse::count());
        $this->assertSame(1, \App\Models\Pharmacist::count());
    }

    public function test_nurse_and_staff_permissions_are_separate(): void
    {
        DB::table('permission')->where('permission_group', 'nurse')
            ->where('permission_name', 'nurse_view')->update(['permission_type' => 'yes']);
        Permissions::flush();

        $nurse = User::create([
            'firstname' => 'A', 'lastname' => 'Nurse', 'login' => 'wardnurse',
            'email' => 'wardnurse@example.test', 'desc' => '', 'group' => 'Nurse',
            'password' => bcrypt('secret'),
        ]);

        // nurse_view was granted, staff_view was not.
        $this->actingAs($nurse)->get('/staff/nurse')->assertOk();
        $this->actingAs($nurse)->get('/staff/accountant')->assertRedirect('/no-permission');
    }

    /* ---------------- departments ---------------- */

    public function test_a_department_can_be_added_and_renamed(): void
    {
        $this->post('/departments', ['name' => 'Cardiology'])->assertSessionHasNoErrors();
        $department = Department::first();
        $this->assertNotNull($department);

        $this->put('/departments/' . $department->id, ['name' => 'Cardiac Sciences'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Cardiac Sciences', $department->fresh()->name);
    }

    /**
     * The reason renaming is handled rather than left to a plain update: the doctor
     * record stores the department as text.
     */
    public function test_renaming_a_department_carries_its_doctors_with_it(): void
    {
        Department::create(['name' => 'Orthopaedics', 'description' => '', 'x' => '', 'y' => '']);
        $department = Department::first();
        Doctor::create(['name' => 'Dr Bone', 'department' => 'Orthopaedics']);
        Doctor::create(['name' => 'Dr Joint', 'department' => 'Orthopaedics']);
        Doctor::create(['name' => 'Dr Heart', 'department' => 'Cardiology']);

        $this->put('/departments/' . $department->id, ['name' => 'Orthopaedic Surgery']);

        $this->assertSame(2, Doctor::where('department', 'Orthopaedic Surgery')->count());
        $this->assertSame(0, Doctor::where('department', 'Orthopaedics')->count());
        $this->assertSame('Cardiology', Doctor::where('name', 'Dr Heart')->value('department'));
    }

    public function test_a_department_with_doctors_is_not_deleted(): void
    {
        Department::create(['name' => 'Radiology', 'description' => '', 'x' => '', 'y' => '']);
        $department = Department::first();
        Doctor::create(['name' => 'Dr Scan', 'department' => 'Radiology']);

        $this->delete('/departments/' . $department->id);

        $this->assertNotNull(Department::find($department->id));
    }

    public function test_an_empty_department_is_deleted(): void
    {
        Department::create(['name' => 'Unused', 'description' => '', 'x' => '', 'y' => '']);
        $department = Department::first();

        $this->delete('/departments/' . $department->id);

        $this->assertNull(Department::find($department->id));
    }

    public function test_the_list_refuses_a_duplicate_department(): void
    {
        $this->post('/departments', ['name' => 'Paediatrics']);
        $this->post('/departments', ['name' => 'Paediatrics'])->assertSessionHasErrors('name');

        $this->assertSame(1, Department::count());
    }

    /**
     * An install that predates this screen has departments only as text on doctor
     * records. Adopting puts them on the list so they can be managed from here.
     */
    public function test_adopt_picks_up_departments_only_used_by_doctors(): void
    {
        Doctor::create(['name' => 'Dr A', 'department' => 'Neurology']);
        Doctor::create(['name' => 'Dr B', 'department' => 'Neurology']);
        Doctor::create(['name' => 'Dr C', 'department' => 'Dermatology']);
        Department::create(['name' => 'Neurology', 'description' => '', 'x' => '', 'y' => '']);

        $this->get('/departments')->assertOk()->assertSee('Dermatology');

        $this->post('/departments/adopt')->assertSessionHasNoErrors();

        $this->assertSame(2, Department::count());
        $this->assertNotNull(Department::where('name', 'Dermatology')->first());
    }

    public function test_the_doctor_form_offers_the_department_list(): void
    {
        Department::create(['name' => 'Oncology', 'description' => '', 'x' => '', 'y' => '']);
        Doctor::create(['name' => 'Dr Legacy', 'department' => 'Typed By Hand']);

        $this->get('/doctors/create')
            ->assertOk()
            ->assertSee('Oncology')
            ->assertSee('Typed By Hand');
    }
}
