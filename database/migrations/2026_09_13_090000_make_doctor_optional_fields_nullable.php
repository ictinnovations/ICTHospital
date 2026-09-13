<?php
/**
 * ICTHospital - let a doctor be added with just a name.
 *
 * Every column on the doctor table was transcribed NOT NULL from the CodeIgniter
 * schema, including img_url, email, address, department, profile and the two
 * unused layout leftovers x and y. The old application wrote empty strings into
 * all of them, so the constraint never bit there.
 *
 * The controller still fills those columns with empty strings for anything that
 * reads the table directly, but a NOT NULL column with no default is a trap for
 * the next person, and a locum who starts on Monday with nothing on file but a
 * name has to be addable.
 *
 * Development databases hide this: hospital:modernise-schema leaves columns it
 * touches nullable, so it only shows on a clean install from migrations, which is
 * what CI and every new deployment does.
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
        'img_url', 'email', 'address', 'phone', 'department',
        'profile', 'x', 'y', 'ion_user_id',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('doctor')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        foreach (self::COLUMNS as $column) {
            if (! Schema::hasColumn('doctor', $column)) {
                continue;
            }

            // Raw statements on MySQL and MariaDB so the existing column type is
            // kept, whatever hospital:modernise-schema may already have made it.
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $type = DB::selectOne(
                    'select column_type as t from information_schema.columns
                      where table_schema = database() and table_name = ? and column_name = ?',
                    ['doctor', $column]
                );

                if ($type) {
                    DB::statement("ALTER TABLE `doctor` MODIFY `{$column}` {$type->t} NULL");
                }

                continue;
            }

            // sqlite cannot drop NOT NULL in place. Laravel's change() rebuilds
            // the table, which is the only route there.
            Schema::table('doctor', function ($table) use ($column) {
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
