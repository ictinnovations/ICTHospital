<?php
/**
 * ICTHospital - administration route access tests.
 *
 * The user, permission, branch and impersonation routes used to sit in a group
 * with no auth middleware, so an anonymous request could log in as any user,
 * delete accounts, rewrite the role grants or read the branch credentials. These
 * tests hold every one of them shut for anonymous visitors and for signed-in
 * staff who are not Admin, and check that an Admin can still use them.
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

class AdminRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user($group, $login)
    {
        return User::create([
            'firstname' => 'Test', 'lastname' => $group, 'login' => $login,
            'email' => $login . '@example.test', 'desc' => '', 'group' => $group,
            'password' => bcrypt('secret'),
        ]);
    }

    private function addBranch()
    {
        DB::table('branches')->insert([
            'branch_name' => 'North', 'branch_url' => 'https://north.example.test',
            'username' => 'north-admin', 'password' => 'branch-secret',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_anonymous_visitor_cannot_log_in_as_another_user(): void
    {
        $admin = $this->user('Admin', 'admin');

        $this->get('/login/' . $admin->id)->assertRedirect('/');
        $this->assertGuest();

        // The old two-segment form of the route must be gone entirely.
        $this->get('/login/' . $admin->id . '/1')->assertNotFound();
        $this->assertGuest();
    }

    public function test_anonymous_visitor_cannot_delete_a_user(): void
    {
        $doctor = $this->user('Doctors', 'doctor');

        $this->get('/userdelete/' . $doctor->id)->assertRedirect('/');

        $this->assertNotNull(User::find($doctor->id));
    }

    public function test_anonymous_visitor_cannot_rewrite_the_role_grants(): void
    {
        $before = DB::table('permission')->orderBy('id')->get()->toArray();
        $this->assertNotEmpty($before);

        $this->get('/permission')->assertRedirect('/');
        $this->post('/permission/create', ['nurse' => ['Patient View']])->assertRedirect('/');

        $this->assertEquals($before, DB::table('permission')->orderBy('id')->get()->toArray());
    }

    public function test_anonymous_visitor_cannot_read_or_replace_branches(): void
    {
        $this->addBranch();

        $this->get('/branches')->assertRedirect('/')->assertDontSee('branch-secret');
        $this->post('/branch', [
            'branchname' => ['Evil'], 'url' => ['https://evil.example.test'],
            'username' => ['x'], 'password' => ['y'],
        ])->assertRedirect('/');

        $this->assertSame(['North'], DB::table('branches')->pluck('branch_name')->all());
    }

    public function test_staff_who_are_not_admin_are_kept_out(): void
    {
        $admin = $this->user('Admin', 'admin');
        $nurse = $this->user('Nurses', 'nurse');
        $doctor = $this->user('Doctors', 'doctor');
        $this->addBranch();
        $before = DB::table('permission')->count();

        $this->actingAs($nurse);

        $this->get('/login/' . $admin->id);
        $this->assertAuthenticatedAs($nurse);

        $this->get('/userdelete/' . $doctor->id);
        $this->assertNotNull(User::find($doctor->id));

        $this->post('/permission/create', ['nurse' => ['Patient View']]);
        $this->assertSame($before, DB::table('permission')->count());

        $this->get('/branches')->assertDontSee('branch-secret');
        $this->get('/users')->assertDontSee('doctor@example.test');
    }

    public function test_admin_can_impersonate_and_logout_returns_to_the_real_admin(): void
    {
        $admin = $this->user('Admin', 'admin');
        $doctor = $this->user('Doctors', 'doctor');

        $this->actingAs($admin);

        $this->get('/login/' . $doctor->id)->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($doctor);
        $this->assertSame($admin->id, session('adminID'));

        $this->get('/users/logout')->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_still_delete_a_user(): void
    {
        $admin = $this->user('Admin', 'admin');
        $doctor = $this->user('Doctors', 'doctor');

        $this->actingAs($admin)->get('/userdelete/' . $doctor->id)->assertRedirect('/users');

        $this->assertNull(User::find($doctor->id));
    }
}
