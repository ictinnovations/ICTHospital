<?php
/**
 * ICTHospital - fixes from the 22 October 2026 QA round.
 *
 * Staff logins from the doctor and staff forms, admin-only areas hidden from and closed
 * to other roles, the activity log for admins, and medical history reports kept private.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\Pharmacist;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QaFixesOct22Test extends TestCase
{
    use RefreshDatabase;

    private function user(string $login, string $group): User
    {
        return User::create([
            'firstname' => 'Test', 'lastname' => $group, 'login' => $login,
            'email' => $login . '@example.test', 'desc' => '', 'group' => $group,
            'password' => bcrypt('secret123'),
        ]);
    }

    private function grant(string $group, array $keys): void
    {
        foreach ($keys as $key) {
            DB::table('permission')->updateOrInsert(
                ['permission_group' => $group, 'permission_name' => $key],
                ['permission_type' => 'yes']
            );
        }
        Permissions::flush();
    }

    private function patient(): Patient
    {
        return Patient::create([
            'name' => 'Amina Yousaf', 'patient_id' => '000001', 'img_url' => '', 'email' => '',
            'doctor' => '', 'address' => '', 'phone' => '', 'sex' => '', 'age' => '',
            'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test',
        ]);
    }

    // 1. Logins for new doctors and staff -------------------------------------------

    public function test_adding_a_doctor_with_a_username_creates_a_linked_doctor_login(): void
    {
        $this->actingAs($this->user('admin1', 'Admin'));

        $this->post('/doctors', ['name' => 'Dr Sana Riaz', 'login_username' => 'dr.sana', 'login_password' => 'Clinic@2026'])
            ->assertSessionHasNoErrors();

        $doctor = Doctor::where('name', 'Dr Sana Riaz')->firstOrFail();
        $login = User::where('login', 'dr.sana')->firstOrFail();
        $this->assertSame('Doctor', $login->group);
        $this->assertSame((string) $login->id, (string) $doctor->ion_user_id);
        $this->assertTrue(Hash::check('Clinic@2026', $login->password));
    }

    public function test_editing_a_doctor_keeps_the_login_and_can_change_its_password(): void
    {
        $this->actingAs($this->user('admin1', 'Admin'));
        $this->post('/doctors', ['name' => 'Dr Sana Riaz', 'login_username' => 'dr.sana', 'login_password' => 'Clinic@2026']);
        $doctor = Doctor::where('name', 'Dr Sana Riaz')->firstOrFail();
        $linked = $doctor->ion_user_id;

        $this->put('/doctors/' . $doctor->id, ['name' => 'Dr Sana Riaz', 'department' => 'Cardiology'])
            ->assertSessionHasNoErrors();
        $this->assertSame((string) $linked, (string) $doctor->fresh()->ion_user_id);

        $this->put('/doctors/' . $doctor->id, ['name' => 'Dr Sana Riaz', 'login_password' => 'NewPass@2026'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPass@2026', User::where('login', 'dr.sana')->first()->password));
    }

    public function test_a_username_cannot_be_reused_and_a_new_login_needs_a_password(): void
    {
        $this->actingAs($this->user('admin1', 'Admin'));
        $this->user('taken', 'Nurse');

        $this->post('/doctors', ['name' => 'Dr A', 'login_username' => 'taken', 'login_password' => 'Clinic@2026'])
            ->assertSessionHasErrors('login_username');
        $this->post('/doctors', ['name' => 'Dr B', 'login_username' => 'dr.b'])
            ->assertSessionHasErrors('login_password');
        $this->assertSame(0, Doctor::count());
    }

    public function test_a_staff_member_gets_a_login_in_their_own_role(): void
    {
        $this->actingAs($this->user('admin1', 'Admin'));

        $this->post('/staff/pharmacist', ['name' => 'Bilal Khan', 'login_username' => 'bilal.pharm', 'login_password' => 'Pharm@2026'])
            ->assertSessionHasNoErrors();

        $person = Pharmacist::where('name', 'Bilal Khan')->firstOrFail();
        $login = User::where('login', 'bilal.pharm')->firstOrFail();
        $this->assertSame('Pharmacist', $login->group);
        $this->assertSame((string) $login->id, (string) $person->ion_user_id);
    }

    public function test_adding_a_doctor_without_login_fields_creates_no_login(): void
    {
        $this->actingAs($this->user('admin1', 'Admin'));
        $before = User::count();

        $this->post('/doctors', ['name' => 'Dr No Login'])->assertSessionHasNoErrors();

        $this->assertSame($before, User::count());
    }

    // 2. Admin areas hidden from and closed to other roles ------------------------------

    public function test_a_doctor_does_not_see_admin_or_accounting_menus_but_has_my_profile(): void
    {
        $this->grant('doctor', ['patient_view']);
        $this->actingAs($this->user('doc1', 'Doctor'));

        $this->get('/dashboard')->assertOk()
            ->assertDontSee('> Accounting<', false)
            ->assertDontSee('> Messaging<', false)
            ->assertDontSee('> Users and Roles<', false)
            ->assertDontSee('> Activity Log<', false)
            ->assertDontSee('Hospital Information')
            ->assertSee('My Profile');
    }

    public function test_an_admin_sees_the_admin_menus(): void
    {
        $this->actingAs($this->user('admin1', 'Admin'));

        $this->get('/dashboard')->assertOk()
            ->assertSee('Messaging')->assertSee('Users and Roles')
            ->assertSee('Hospital Information')->assertSee('Activity Log');
    }

    public function test_admin_pages_are_closed_to_other_roles_by_url(): void
    {
        $this->actingAs($this->user('doc1', 'Doctor'));

        foreach (['/institute', '/ictcore', '/template/list', '/message', '/smslog', '/schedule', '/activity'] as $url) {
            $this->get($url)->assertRedirect('page-not-found');
        }
        // Everyone keeps their own profile page.
        $this->get('/settings')->assertOk();
    }

    public function test_the_activity_log_opens_for_an_admin(): void
    {
        $this->actingAs($this->user('admin1', 'Admin'));

        $this->get('/activity')->assertOk();
    }

    public function test_a_doctor_with_the_grant_can_register_a_patient(): void
    {
        $this->grant('doctor', ['patient_view', 'patient_add']);
        $this->actingAs($this->user('doc1', 'Doctor'));

        $this->get('/patients/create')->assertOk();
        $this->post('/patients', ['name' => 'Walk In Patient'])->assertSessionHasNoErrors();
        $this->assertTrue(Patient::where('name', 'Walk In Patient')->exists());
    }

    // 3. Medical history reports are private ----------------------------------------------

    public function test_an_uploaded_report_is_private_and_opens_only_for_staff_with_access(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $patient = $this->patient();
        $this->grant('doctor', ['patient_view', 'patient_medical_history']);
        $doctor = $this->user('doc1', 'Doctor');
        $this->actingAs($doctor);

        $this->post('/history', [
            'patient_id' => $patient->id, 'title' => 'Chest X-ray', 'description' => 'Clear.',
            'attachment' => UploadedFile::fake()->image('xray.jpg'),
        ])->assertSessionHasNoErrors();

        $entry = MedicalHistory::firstOrFail();
        Storage::disk('local')->assertExists($entry->img_url);
        Storage::disk('public')->assertMissing($entry->img_url);

        $this->get('/history/' . $entry->id . '/attachment')->assertOk();

        // A role without the permission is turned away.
        $this->actingAs($this->user('acc1', 'Accountant'));
        $this->get('/history/' . $entry->id . '/attachment')->assertRedirect();

        // Signed out: straight to the login page.
        auth()->logout();
        $this->flushSession();
        $this->get('/history/' . $entry->id . '/attachment')->assertRedirect();
    }

    public function test_an_old_public_report_is_moved_to_private_storage_when_opened(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $patient = $this->patient();
        Storage::disk('public')->put('history/old-scan.jpg', 'scan');
        $entry = MedicalHistory::create([
            'patient_id' => $patient->id, 'title' => 'Old scan', 'description' => 'From 2025.',
            'img_url' => 'history/old-scan.jpg', 'date' => '2025-01-01', 'patient_name' => $patient->name,
            'patient_address' => '', 'patient_phone' => '',
        ]);
        $this->grant('doctor', ['patient_medical_history']);
        $this->actingAs($this->user('doc1', 'Doctor'));

        $this->get('/history/' . $entry->id . '/attachment')->assertOk();

        Storage::disk('public')->assertMissing('history/old-scan.jpg');
        Storage::disk('local')->assertExists('history/old-scan.jpg');
    }
}
