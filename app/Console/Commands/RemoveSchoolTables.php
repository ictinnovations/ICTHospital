<?php
/**
 * ICTHospital - report on, and optionally apply, removal of the school tables.
 *
 *   php artisan hospital:remove-school-tables            report only
 *   php artisan hospital:remove-school-tables --apply    drop the empty ones
 *   php artisan hospital:remove-school-tables --apply --force
 *                                                        drop them even with rows
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Console\Commands;

use App\Services\SchoolTableRemover;
use Illuminate\Console\Command;

class RemoveSchoolTables extends Command
{
    protected $signature = 'hospital:remove-school-tables
                            {--apply : drop the tables instead of only reporting}
                            {--force : drop them even when they still hold rows}';

    protected $description = 'Remove the ICTSchool tables the hospital build no longer uses';

    public function handle()
    {
        $apply = (bool) $this->option('apply');
        $force = (bool) $this->option('force');

        if ($apply && $force && ! $this->option('no-interaction')) {
            if (! $this->confirm('--force discards rows in those tables. Do you have a backup?', false)) {
                return self::FAILURE;
            }
        }

        $this->info($apply
            ? 'Dropping school tables.'
            : 'Reporting only. Add --apply to drop them.');
        $this->newLine();

        $log = (new SchoolTableRemover())->run($apply, $force);

        $this->table(['Status', 'Table', 'Detail'], array_map(function ($row) {
            return [$row['status'], $row['table'], $row['detail']];
        }, $log));

        $counts = [];
        foreach ($log as $row) {
            $counts[$row['status']] = ($counts[$row['status']] ?? 0) + 1;
        }

        foreach ($counts as $status => $n) {
            $line = $n . ' ' . $status;
            $status === 'skip' ? $this->warn($line) : $this->info($line);
        }

        return self::SUCCESS;
    }
}
