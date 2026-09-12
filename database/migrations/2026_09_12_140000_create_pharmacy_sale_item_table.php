<?php
/**
 * ICTHospital - give a pharmacy sale real line items.
 *
 * pharmacy_payment.category_name and category_amount are the same packed string
 * pair found on prescriptions and lab requests: every item on the sale in one
 * column, every price in another, positionally matched. Nothing could be asked of
 * it, and nothing decremented stock, so the quantity column on the medicine
 * catalogue only ever moved when somebody edited it by hand.
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
        'pharmacy_payment' => [
            'category', 'patient', 'doctor', 'date', 'x_ray', 'flat_vat', 'flat_discount',
            'gross_total', 'hospital_amount', 'doctor_amount', 'category_amount',
            'category_name', 'amount_received', 'status',
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('pharmacy_sale_item')) {
            Schema::create('pharmacy_sale_item', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('pharmacy_payment_id')->index();
                $table->unsignedInteger('medicine_id')->nullable()->index();

                // Name and price are copied in. A sale is a financial record of
                // what was handed over and charged on the day, so it must not
                // change when the catalogue price is next updated.
                $table->string('name', 200);
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('line_total', 12, 2)->default(0);
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
                    $long = ['category_amount', 'category_name'];
                    $blueprint->string($column, in_array($column, $long, true) ? 1000 : 100)
                        ->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_sale_item');
    }
};
