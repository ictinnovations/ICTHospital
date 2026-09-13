<?php
/**
 * ICTHospital - base test case.
 *
 * Every clinical route now sits behind the permission catalogue, and a test
 * database built by RefreshDatabase has an empty permission table, so an Admin
 * created inside a test would be denied every screen. A real install is never in
 * that state: the seeder fills the catalogue and grants all of it to Admin.
 *
 * Seeding it here reproduces a real install rather than a half built one. Tests
 * that are about the permission system itself clear the table in their own setUp
 * and grant exactly what they mean to.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests;

use App\Support\Permissions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    /** Set false in a test class that wants to control the grants itself. */
    protected $seedPermissions = true;

    protected function setUp(): void
    {
        parent::setUp();

        Permissions::flush();

        if ($this->seedPermissions && Schema::hasTable('permission') && DB::table('permission')->count() === 0) {
            $this->artisan('hospital:sync-permissions');
            Permissions::flush();
        }
    }
}
