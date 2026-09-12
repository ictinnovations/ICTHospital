<?php
/**
 * ICTHospital - let bed and admission rows hold real nulls.
 *
 * Third table group with the same inherited problem: the CodeIgniter schema
 * declared every column NOT NULL and the application wrote empty strings, so a
 * clean install rejects rows that a modernised development database accepts.
 *
 * It matters more here than elsewhere. `alloted_bed.d_time` being null is what
 * marks a patient as still on the ward, and it is the single thing the whole
 * module uses to decide whether a bed is free. With the column NOT NULL there is
 * no way to express "has not been discharged" other than an empty string, which
 * is exactly the ambiguity this replaces.
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
    private const TARGETS = [
        'bed' => ['description', 'last_a_time', 'last_d_time', 'status', 'bed_id'],
        'bed_category' => ['description'],
        'alloted_bed' => ['number', 'category', 'a_time', 'd_time', 'status', 'x', 'bed_id'],
    ];

    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        foreach (self::TARGETS as $table => $columns) {
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

                Schema::table($table, function ($blueprint) use ($column) {
                    $blueprint->string($column, 100)->nullable()->change();
                });
            }
        }

        // Existing rows written by the old application use an empty string where
        // they mean "not discharged". Left alone they would read as occupied
        // forever, because the module tests for null.
        if (Schema::hasTable('alloted_bed')) {
            DB::table('alloted_bed')->where('d_time', '')->update(['d_time' => null]);
        }
    }

    public function down(): void
    {
        // Not reversed. Restoring NOT NULL would make an open admission
        // impossible to represent.
    }
};
