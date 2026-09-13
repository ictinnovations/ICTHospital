<?php
/**
 * ICTHospital - nullable columns for medical history and payment gateways.
 *
 * The same NOT NULL inheritance that hit every other legacy table. A history entry
 * with no attachment, or a gateway configured with only an API username, should be
 * savable, and the controllers write empty strings for anything reading these tables
 * directly.
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
    private const TABLES = [
        // date and registration_time are declared NOT NULL by the create migration.
        // hospital:modernise-schema loosens them on any database it has run against,
        // so this only bites on a clean install, which is exactly where it matters.
        'medical_history' => [
            'patient_name', 'patient_address', 'patient_phone', 'img_url',
            'date', 'registration_time',
        ],
        'paymentGateway' => ['merchant_key', 'salt', 'x', 'y', 'APIUsername', 'APIPassword', 'APISignature', 'status'],
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

                // sqlite cannot drop NOT NULL in place; change() rebuilds the table.
                // The type has to be restated, so the two date columns keep theirs
                // instead of being rebuilt as strings.
                Schema::table($table, function ($t) use ($column) {
                    if ($column === 'date') {
                        $t->date($column)->nullable()->change();
                    } elseif ($column === 'registration_time') {
                        $t->dateTime($column)->nullable()->change();
                    } else {
                        $t->string($column, 1000)->nullable()->change();
                    }
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
