<?php
/**
 * ICTHospital - let an appointment be stored without the legacy filler columns.
 *
 * Same problem as the patient table: the appointment schema was transcribed from
 * CodeIgniter, where every column was NOT NULL and the application wrote empty
 * strings rather than nulls. A clean install from migrations therefore rejects a
 * booking that leaves any of them unset, while a development database that has
 * run hospital:modernise-schema accepts it, so the failure only appears on a
 * fresh deployment or in CI.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = [
        'date', 's_time', 'e_time', 'add_date', 'registration_time',
        'time_slot', 's_time_key', 'remarks', 'status', 'user', 'request',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('appointment')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        foreach (self::COLUMNS as $column) {
            if (! Schema::hasColumn('appointment', $column)) {
                continue;
            }

            // The column type differs between a fresh install (varchar) and one
            // that has been modernised (DATE, DATETIME), so the existing type is
            // read back and reused rather than assumed.
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $type = DB::selectOne(
                    'select column_type as t from information_schema.columns
                      where table_schema = database() and table_name = ? and column_name = ?',
                    ['appointment', $column]
                );

                if ($type) {
                    DB::statement("ALTER TABLE `appointment` MODIFY `{$column}` {$type->t} NULL");
                }

                continue;
            }

            Schema::table('appointment', function ($table) use ($column) {
                $length = $column === 'remarks' ? 500 : 100;
                $table->string($column, $length)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Not reversed. Restoring NOT NULL would reject bookings this release
        // allows people to create.
    }
};
