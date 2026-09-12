<?php
/**
 * ICTHospital - let a patient be registered without a date of birth.
 *
 * The patient table was transcribed from the CodeIgniter schema, where every
 * column was NOT NULL and the application wrote empty strings into the ones it
 * had no value for. `birthdate` is declared NOT NULL there, so a front desk
 * registering a walk-in who does not know their date of birth hits an integrity
 * constraint rather than a saved record.
 *
 * Development databases hid this: hospital:modernise-schema converts birthdate to
 * a real DATE and leaves it nullable, so the bug only shows on a clean install
 * from migrations, which is exactly what CI and any new deployment does.
 *
 * add_date is included for the same reason. It is stamped by the controller
 * today, but nothing should force a date column to hold a value it does not have.
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
    /** Columns that should accept NULL, as column => whether it holds a date. */
    private const COLUMNS = ['birthdate', 'add_date', 'registration_time'];

    public function up(): void
    {
        if (! Schema::hasTable('patient')) {
            return;
        }

        // Done with raw statements rather than ->change(), because the column type
        // differs between a fresh install (varchar, from the migration above) and
        // an install that has run hospital:modernise-schema (DATE or DATETIME).
        // Reading the existing type and reusing it keeps both cases intact.
        foreach (self::COLUMNS as $column) {
            if (! Schema::hasColumn('patient', $column)) {
                continue;
            }

            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql' || $driver === 'mariadb') {
                $type = DB::selectOne(
                    'select column_type as t from information_schema.columns
                      where table_schema = database() and table_name = ? and column_name = ?',
                    ['patient', $column]
                );

                if ($type) {
                    DB::statement("ALTER TABLE `patient` MODIFY `{$column}` {$type->t} NULL");
                }

                continue;
            }

            // sqlite cannot drop a NOT NULL constraint in place. Laravel's change()
            // rebuilds the table, which is the only route there.
            Schema::table('patient', function ($table) use ($column) {
                $table->string($column, 100)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Deliberately not reversed. Putting NOT NULL back would reject records
        // this release allows people to create.
    }
};
