<?php
/**
 * ICTHospital - add catalogue permissions that are missing from the table.
 *
 * The seeder only fills the permission table when it is empty, so an install
 * upgraded from ICTSchool keeps its old rows: student_view, class_add, exam_marks
 * and the rest. None of the hospital keys exist there, so the moment the clinical
 * routes started checking permissions, every role including Admin would have been
 * locked out of every screen.
 *
 * This inserts only what is missing. Rights that were deliberately taken away from
 * a role are left alone, because a re-run must never quietly hand them back.
 *
 * Usage:
 *   php artisan hospital:sync-permissions              add what is missing
 *   php artisan hospital:sync-permissions --prune      also drop keys not in the catalogue
 *   php artisan hospital:sync-permissions --dry-run    report and change nothing
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Console\Commands;

use App\Support\Permissions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncPermissions extends Command
{
    protected $signature = 'hospital:sync-permissions
                            {--prune : remove stored permissions that are not in the catalogue}
                            {--dry-run : report what would change and write nothing}';

    protected $description = 'Insert any catalogue permissions missing from the permission table, granting them to Admin';

    public function handle()
    {
        if (! Schema::hasTable('permission')) {
            $this->error('No permission table. Run php artisan migrate first.');

            return 1;
        }

        $groups = array_keys(config('hospital_permissions.groups', []));
        $labels = config('hospital_permissions.permissions', []);

        if (empty($groups) || empty($labels)) {
            $this->error('The permission catalogue in config/hospital_permissions.php is empty.');

            return 1;
        }

        $dryRun = (bool) $this->option('dry-run');
        $wanted = [];
        foreach ($labels as $label) {
            $wanted[] = Permissions::key($label);
        }

        $existing = DB::table('permission')
            ->get(['permission_name', 'permission_group'])
            ->groupBy('permission_group')
            ->map(fn ($rows) => $rows->pluck('permission_name')->flip());

        $added = 0;
        $now = now();

        foreach ($groups as $group) {
            $have = $existing[$group] ?? collect();

            foreach ($wanted as $key) {
                if (isset($have[$key])) {
                    continue;
                }

                $added++;

                if ($dryRun) {
                    continue;
                }

                DB::table('permission')->insert([
                    'permission_name' => $key,
                    'permission_group' => $group,
                    // Admin gets the whole catalogue so an upgrade cannot lock the
                    // only account that can hand rights out. Everyone else starts
                    // with nothing and is granted what they need on the grid.
                    'permission_type' => $group === 'admin' ? 'yes' : '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $stale = DB::table('permission')->whereNotIn('permission_name', $wanted)->count();

        if ($this->option('prune') && $stale > 0 && ! $dryRun) {
            DB::table('permission')->whereNotIn('permission_name', $wanted)->delete();
            $this->line('Removed ' . $stale . ' permission(s) that are no longer in the catalogue.');
            $stale = 0;
        }

        Permissions::flush();

        $this->info(($dryRun ? 'Would add ' : 'Added ') . $added . ' permission row(s) across ' . count($groups) . ' role(s).');

        if ($stale > 0) {
            $this->warn($stale . ' stored permission(s) are not in the catalogue, most likely left over from ICTSchool.');
            $this->warn('They are harmless but you can clear them with --prune.');
        }

        return 0;
    }
}
