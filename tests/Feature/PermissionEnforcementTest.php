<?php
/**
 * ICTHospital - permission enforcement feature tests.
 *
 * The 90 entry catalogue existed and the grid edited it, but no clinical route
 * checked it, so anyone with an account reached every screen. These tests hold
 * that shut: an Admin gets through, a role with nothing granted does not, and
 * granting the one right it needs is enough to let it in.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    /** These tests own the grant table, so the base case must not fill it in. */
    protected $seedPermissions = false;

    protected function setUp(): void
    {
        parent::setUp();
        Permissions::flush();
    }

    private function user($group, $login)
    {
        return User::create([
            'firstname' => 'Test', 'lastname' => ucfirst($group), 'login' => $login,
            'email' => $login . '@example.test', 'desc' => '', 'group' => ucfirst($group),
            'password' => bcrypt('secret'),
        ]);
    }

    private function grant($group, $permission)
    {
        DB::table('permission')->insert([
            'permission_name' => $permission,
            'permission_group' => $group,
            'permission_type' => 'yes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Permissions::flush();
    }

    public function test_sync_permissions_fills_the_catalogue_and_grants_it_to_admin(): void
    {
        $this->assertSame(0, DB::table('permission')->count());

        Artisan::call('hospital:sync-permissions');

        $catalogue = count(config('hospital_permissions.permissions'));
        $roles = count(config('hospital_permissions.groups'));

        $this->assertSame($catalogue * $roles, DB::table('permission')->count());
        $this->assertSame(
            $catalogue,
            DB::table('permission')->where('permission_group', 'admin')->where('permission_type', 'yes')->count()
        );
        $this->assertSame(
            0,
            DB::table('permission')->where('permission_group', 'nurse')->where('permission_type', 'yes')->count()
        );
    }

    public function test_sync_permissions_does_not_regrant_a_right_that_was_taken_away(): void
    {
        Artisan::call('hospital:sync-permissions');

        DB::table('permission')
            ->where('permission_group', 'admin')
            ->where('permission_name', 'patient_delete')
            ->update(['permission_type' => '']);

        Artisan::call('hospital:sync-permissions');

        $this->assertSame('', DB::table('permission')
            ->where('permission_group', 'admin')
            ->where('permission_name', 'patient_delete')
            ->value('permission_type'));
    }

    public function test_sync_permissions_leaves_school_leftovers_alone_unless_pruned(): void
    {
        DB::table('permission')->insert([
            'permission_name' => 'student_view', 'permission_group' => 'admin',
            'permission_type' => 'yes', 'created_at' => now(), 'updated_at' => now(),
        ]);

        Artisan::call('hospital:sync-permissions');
        $this->assertSame(1, DB::table('permission')->where('permission_name', 'student_view')->count());

        Artisan::call('hospital:sync-permissions', ['--prune' => true]);
        $this->assertSame(0, DB::table('permission')->where('permission_name', 'student_view')->count());
    }

    public function test_an_admin_with_the_catalogue_reaches_the_patient_list(): void
    {
        Artisan::call('hospital:sync-permissions');

        $this->actingAs($this->user('admin', 'permadmin'))
            ->get('/patients')
            ->assertOk();
    }

    public function test_a_role_with_nothing_granted_is_turned_away(): void
    {
        Artisan::call('hospital:sync-permissions');

        $this->actingAs($this->user('nurse', 'permnurse'))
            ->get('/patients')
            ->assertRedirect('/no-permission');
    }

    public function test_granting_the_single_right_lets_that_role_in(): void
    {
        Artisan::call('hospital:sync-permissions');
        $this->grant('nurse', 'patient_view');

        $this->actingAs($this->user('nurse', 'permnurse2'))
            ->get('/patients')
            ->assertOk();
    }

    public function test_view_does_not_carry_delete_with_it(): void
    {
        Artisan::call('hospital:sync-permissions');
        $this->grant('nurse', 'patient_view');

        $this->actingAs($this->user('nurse', 'permnurse3'))
            ->delete('/patients/1')
            ->assertRedirect('/no-permission');
    }

    public function test_the_no_permission_page_explains_itself(): void
    {
        Artisan::call('hospital:sync-permissions');

        $this->actingAs($this->user('nurse', 'permnurse4'))
            ->get('/no-permission')
            ->assertOk()
            ->assertSee('do not have access');
    }

    public function test_an_unauthenticated_request_goes_to_the_login_page_not_a_fatal(): void
    {
        Artisan::call('hospital:sync-permissions');

        $this->get('/patients')->assertRedirect();
    }

    public function test_the_menu_hides_what_the_role_cannot_open(): void
    {
        Artisan::call('hospital:sync-permissions');
        $this->grant('nurse', 'patient_view');

        $this->actingAs($this->user('nurse', 'permnurse5'))
            ->get('/patients')
            ->assertOk()
            ->assertSee('Patients')
            ->assertDontSee('Services and Prices');
    }
}
