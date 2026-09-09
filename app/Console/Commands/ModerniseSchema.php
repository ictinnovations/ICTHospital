<?php
/**
 * ICTHospital - report on, and optionally apply, the schema modernisation.
 *
 *   php artisan hospital:modernise-schema            report only, changes nothing
 *   php artisan hospital:modernise-schema --apply    make the safe changes
 *
 * Read the report before applying. Anything marked "skip" needs the data cleaning
 * up first, and the reason says what is wrong with it.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Console\Commands;

use App\Services\SchemaModerniser;
use Illuminate\Console\Command;

class ModerniseSchema extends Command
{
    protected $signature = 'hospital:modernise-schema
                            {--apply : make the changes instead of only reporting them}';

    protected $description = 'Convert legacy string dates to date columns and add foreign keys where the data allows';

    public function handle()
    {
        $apply = (bool) $this->option('apply');

        if ($apply && ! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        $this->info($apply ? 'Applying schema changes.' : 'Reporting only. Add --apply to make the changes.');
        $this->newLine();

        $log = (new SchemaModerniser())->run($apply);

        if (empty($log)) {
            $this->info('Nothing to do.');

            return self::SUCCESS;
        }

        $this->table(['Status', 'Column', 'Detail'], array_map(function ($row) {
            return [$row['status'], $row['target'], $row['detail']];
        }, $log));

        $counts = [];
        foreach ($log as $row) {
            $counts[$row['status']] = ($counts[$row['status']] ?? 0) + 1;
        }

        foreach ($counts as $status => $n) {
            $line = $n . ' ' . $status;
            if ($status === 'skip') {
                $this->warn($line);
            } else {
                $this->info($line);
            }
        }

        return self::SUCCESS;
    }

    /**
     * Applying this alters live tables, so ask unless the caller says not to.
     */
    protected function confirmToProceed()
    {
        if ($this->option('no-interaction') || app()->environment('testing')) {
            return true;
        }

        return $this->confirm('This alters table definitions. Do you have a backup?', false);
    }
}
