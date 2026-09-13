<?php
/**
 * ICTHospital - let staff and departments be added with just a name.
 *
 * The same NOT NULL inheritance that hit patient, appointment, ward and doctor. Every
 * column on nurse, pharmacist, laboratorist, receptionist, accountant and department
 * was transcribed NOT NULL from the CodeIgniter schema because the old application
 * wrote empty strings rather than nulls, including the unused layout leftovers x, y
 * and z.
 *
 * The controllers still write empty strings for anything reading those tables
 * directly, but a NOT NULL column with no default is a trap for whoever inserts a row
 * next, and it only shows on a clean install from migrations, which is what CI and
 * every new deployment does.
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
    /** Table => the columns on it that should accept NULL. `name` never does. */
    private const TABLES = [
        'nurse' => ['img_url', 'email', 'address', 'phone', 'x', 'y', 'z', 'ion_user_id'],
        'pharmacist' => ['img_url', 'email', 'address', 'phone', 'x', 'y', 'ion_user_id'],
        'laboratorist' => ['img_url', 'email', 'address', 'phone', 'x', 'y', 'ion_user_id'],
        'receptionist' => ['img_url', 'email', 'address', 'phone', 'x', 'ion_user_id'],
        'accountant' => ['img_url', 'email', 'address', 'phone', 'x', 'ion_user_id'],
        'department' => ['description', 'x', 'y'],
    ];

    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        foreach (self::TABLES as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                // Raw statements on MySQL and MariaDB so the existing column type is
                // kept, whatever hospital:modernise-schema may already have made it.
                if ($driver === 'mysql' || $driver === 'mariadb') {
                    $type = DB::selectOne(
                        'select column_type as t from information_schema.columns
                          where table_schema = database() and table_name = ? and column_name = ?',
                        [$table, $column]
                    );

                    if ($type) {
                        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$type->t} NULL");
                    }

                    continue;
                }

                // sqlite cannot drop NOT NULL in place; change() rebuilds the table,
                // which is the only route there.
                Schema::table($table, function ($t) use ($column) {
                    $t->string($column, 1000)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        // Deliberately not reversed. Putting NOT NULL back would reject records this
        // release allows people to create.
    }
};
