<?php
/**
 * ICTHospital - give a prescription real line items.
 *
 * The legacy `prescription.medicine` column is a varchar(1000) that the
 * CodeIgniter application packed the whole drug list into as one string. Nothing
 * could be queried out of it: you could not ask which patients were on a drug
 * that had just been recalled, or what a pharmacy needed to dispense, without
 * parsing free text.
 *
 * This adds the child table that should always have been there. The old column
 * is kept and filled with a readable summary, so anything still reading it keeps
 * working and old rows are not stranded.
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
    /** Legacy columns that should accept null rather than an empty string. */
    private const NULLABLE = [
        'prescription' => ['date', 'symptom', 'advice', 'state', 'dd', 'medicine', 'validity', 'note'],
        'medicine' => ['category', 'price', 'box', 's_price', 'generic', 'company', 'effects', 'e_date', 'add_date'],
        'medicine_category' => ['description'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('prescription_medicine')) {
            Schema::create('prescription_medicine', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('prescription_id')->index();
                $table->unsignedInteger('medicine_id')->nullable()->index();

                // The name is copied in as well as referenced. A prescription is a
                // clinical record: what was written has to stay readable even if
                // the drug is later renamed or removed from the catalogue.
                $table->string('name', 200);

                $table->string('dosage', 100)->nullable();
                $table->string('duration', 100)->nullable();
                $table->string('instructions', 500)->nullable();
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
                    $length = in_array($column, ['advice', 'medicine', 'note'], true) ? 1000 : 100;
                    $blueprint->string($column, $length)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_medicine');
        // The nullable changes are not reversed; restoring NOT NULL would reject
        // records this release allows.
    }
};
