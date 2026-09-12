<?php
/**
 * ICTHospital - give a lab request real line items.
 *
 * Same shape of problem as the prescription table. `lab.category_name` is a
 * varchar(1000) holding every test on the request as one string, and `lab.report`
 * is a varchar(10000) holding all the results as another. Nothing could be asked
 * of either: not which requests are still waiting on a haemoglobin result, not
 * what a given test has read for one patient over time.
 *
 * This adds the child table. Each row is one test on one request, with its own
 * result and status, so a request can be part reported. The legacy columns are
 * kept and filled with readable summaries.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const NULLABLE = [
        'lab' => [
            'category', 'patient', 'doctor', 'date', 'category_name', 'report', 'status',
            'user', 'patient_name', 'patient_phone', 'patient_address', 'doctor_name', 'date_string',
        ],
        'lab_category' => ['description', 'reference_value'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('lab_test')) {
            Schema::create('lab_test', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('lab_id')->index();
                $table->unsignedInteger('lab_category_id')->nullable()->index();

                // Name and reference range are copied in, not just referenced. A
                // result is only interpretable against the range that applied when
                // it was measured, and laboratories revise their ranges.
                $table->string('name', 200);
                $table->string('reference_value', 1000)->nullable();

                $table->string('result', 1000)->nullable();
                $table->string('status', 40)->default('requested');
                $table->string('reported_at', 100)->nullable();
            });
        }

        $driver = DB::connection()->getDriverName();

        foreach (self::NULLABLE as $table => $columns) {
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
                    $lengths = ['report' => 10000, 'category_name' => 1000, 'reference_value' => 1000];
                    $blueprint->string($column, $lengths[$column] ?? 100)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test');
    }
};
