<?php
/**
 * ICTHospital - the minimum a fresh install needs to be usable.
 *
 * The version this replaces came from ICTSchool. It loaded seven SQL dumps of
 * demo school data, students, classes, sections, subjects, marks, grades and
 * teachers, and six of those files were never committed, so `migrate --seed`
 * failed on every clean install. It also seeded a previous customer's name and
 * website as the institute.
 *
 * This seeds an administrator, a blank hospital record, the permission
 * catalogue with everything granted to Admin, and the two notification types the
 * reminder jobs look for. No demo patients: a hospital system should not ship
 * with invented people in it.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Database\Seeders;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->users();
        $this->hospital();
        $this->permissions();
        $this->notificationTypes();
    }

    /**
     * One administrator. Change the password before the server faces anything.
     */
    protected function users(): void
    {
        if (DB::table('users')->count() > 0) {
            $this->command->info('Users already present, left alone.');

            return;
        }

        User::create([
            'firstname' => 'Hospital',
            'lastname' => 'Administrator',
            'login' => 'admin',
            'email' => 'admin@example.com',
            'group' => 'Admin',
            'desc' => 'Default administrator created by the installer',
            'password' => Hash::make('123456'),
        ]);

        $this->command->warn('Created the admin account with the default password. Change it now.');
    }

    /**
     * A blank hospital record so Settings has something to edit. The name is
     * used as the SMS sender mask, so it is worth setting properly.
     */
    protected function hospital(): void
    {
        if (Institute::count() > 0) {
            return;
        }

        Institute::create([
            'name' => 'ICTHospital',
            'establish' => date('Y'),
            'email' => '',
            'web' => '',
            'phoneNo' => '',
            'address' => '',
        ]);

        $this->command->info('Hospital record created. Set your details in Settings.');
    }

    /**
     * The permission catalogue, with every right granted to Admin and none to
     * the other roles. Nothing is granted quietly.
     */
    protected function permissions(): void
    {
        if (! Schema::hasTable('permission') || DB::table('permission')->count() > 0) {
            return;
        }

        $groups = array_keys(config('hospital_permissions.groups', []));
        $fields = config('hospital_permissions.permissions', []);

        if (empty($groups) || empty($fields)) {
            $this->command->warn('No permission catalogue in config, skipped.');

            return;
        }

        $rows = [];

        foreach ($groups as $group) {
            foreach ($fields as $field) {
                $rows[] = [
                    'permission_name' => str_replace(' ', '_', strtolower($field)),
                    'permission_group' => $group,
                    'permission_type' => $group === 'admin' ? 'yes' : 'no',
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('permission')->insert($chunk);
        }

        $this->command->info(sprintf(
            '%d permissions seeded across %d roles, all granted to Admin.',
            count($fields), count($groups)
        ));
    }

    /**
     * The reminder jobs look these up before they do anything. Seeding them as
     * SMS means the jobs report a missing ICTCore integration rather than a
     * missing notification type, which is the more useful message.
     */
    protected function notificationTypes(): void
    {
        if (! Schema::hasTable('notification_type')) {
            return;
        }

        foreach (['appointment', 'payment'] as $name) {
            if (DB::table('notification_type')->where('notification', $name)->exists()) {
                continue;
            }

            DB::table('notification_type')->insert([
                'notification' => $name,
                'type' => 'sms',
            ]);
        }
    }
}
