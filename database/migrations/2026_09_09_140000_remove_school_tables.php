<?php
/**
 * ICTHospital - drop the school tables on an existing install.
 *
 * The migrations that created these tables have been deleted, so a fresh install
 * never makes them. This removes them from a database that already has them.
 *
 * Empty tables only. Anything holding rows is left alone and logged, so nobody
 * loses data to a migration. Run `php artisan hospital:remove-school-tables`
 * first to see the report, and `--apply --force` if you want them gone anyway.
 *
 * There is no down(). Recreating thirty-three empty school tables would serve
 * nobody, and the data could not come back with them.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use App\Services\SchoolTableRemover;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        foreach ((new SchoolTableRemover())->run(true, false) as $row) {
            $line = sprintf('[%s] %s - %s', $row['status'], $row['table'], $row['detail']);

            if ($row['status'] === 'skip') {
                Log::warning('school table removal ' . $line);
            } else {
                Log::info('school table removal ' . $line);
            }
        }
    }

    public function down(): void
    {
        // Deliberately empty. See the note at the top of this file.
    }
};
