<?php
/**
 * ICTHospital - modernise the legacy schema.
 *
 * Converts varchar date columns to DATE or DATETIME, converts the varchar columns
 * that hold a parent row's id to integers, and adds the foreign keys. Each change
 * is checked against the data first and skipped if it would fail or lose
 * information, so this is safe to run on an existing install as well as a fresh
 * one. Run `php artisan hospital:modernise-schema` beforehand to see the report.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use App\Services\SchemaModerniser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $moderniser = new SchemaModerniser();

        foreach ($moderniser->run(true) as $row) {
            $line = sprintf('[%s] %s - %s', $row['status'], $row['target'], $row['detail']);

            if ($row['status'] === 'skip') {
                Log::warning('schema modernisation ' . $line);
            } else {
                Log::info('schema modernisation ' . $line);
            }
        }
    }

    public function down(): void
    {
        // Only the foreign keys are reversed. Turning a real DATE back into a
        // varchar would be a downgrade with nothing to gain, and the integer
        // columns are the right type whether or not a constraint sits on them.
        (new SchemaModerniser())->dropForeignKeys();
    }
};
