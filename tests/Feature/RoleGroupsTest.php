<?php
/**
 * ICTHospital - user roles line up with the permission catalogue.
 *
 * The user form used to offer Director, Receptionist and Staff, none of which had a
 * column in the permission grid, so those users could never be given any access.
 * Roles now come from config/hospital_permissions.php, which includes the front desk.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleGroupsTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_is_a_grantable_role(): void
    {
        $this->assertArrayHasKey('receptionist', config('hospital_permissions.groups'));
        $this->assertTrue(DB::table('permission')->where('permission_group', 'receptionist')->exists(),
            'the seeded catalogue should have a receptionist column');
    }

    public function test_a_receptionist_with_patient_view_can_open_patients(): void
    {
        DB::table('permission')->where(['permission_group' => 'receptionist', 'permission_name' => 'patient_view'])
            ->update(['permission_type' => 'yes']);

        $this->actingAs(User::create([
            'firstname' => 'Front', 'lastname' => 'Desk', 'login' => 'frontdesk',
            'email' => 'desk@example.test', 'desc' => '', 'group' => 'Receptionist',
            'password' => bcrypt('secret'),
        ]));

        $this->get('/patients')->assertOk();
        $this->get('/invoices')->assertRedirect();
    }

    public function test_the_user_form_offers_only_real_roles(): void
    {
        $this->actingAs(User::create([
            'firstname' => 'Test', 'lastname' => 'Admin', 'login' => 'roleadmin',
            'email' => 'roles@example.test', 'desc' => '', 'group' => 'Admin',
            'password' => bcrypt('secret'),
        ]));

        $this->get('/users')
            ->assertOk()
            ->assertSee('<option value="Receptionist">Receptionist</option>', false)
            ->assertSee('<option value="Accountant">Accountant</option>', false)
            ->assertDontSee('<option value="Director">Director</option>', false)
            ->assertDontSee('<option value="Staff">Staff</option>', false);
    }
}
