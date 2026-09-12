<?php
/**
 * ICTHospital - give an invoice real line items.
 *
 * The last of the packed string pairs. payment.category_name holds every charge
 * on the invoice and payment.category_amount holds every price, matched by
 * position, so a mismatched pair silently shifts every amount onto the wrong
 * service. Nothing could be totalled or audited without parsing two strings and
 * trusting they lined up.
 *
 * Payments against an invoice go in their own table too. The legacy schema had a
 * single `amount_received` column, which cannot represent the normal case of a
 * patient paying a deposit now and the balance later.
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
        'payment' => [
            'category', 'patient', 'doctor', 'date', 'x_ray', 'flat_vat', 'flat_discount',
            'remarks', 'hospital_amount', 'doctor_amount', 'category_amount', 'category_name',
            'amount_received', 'deposit_type', 'status', 'user', 'patient_name',
            'patient_phone', 'patient_address', 'doctor_name', 'date_string',
        ],
        'payment_category' => ['description', 'c_price', 'type', 'd_commission', 'h_commission'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('invoice_item')) {
            Schema::create('invoice_item', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('payment_id')->index();
                $table->unsignedInteger('payment_category_id')->nullable()->index();

                // Copied in for the same reason as everywhere else in this system:
                // an invoice is a financial record of what was charged on the day
                // and must not change when the price list is next edited.
                $table->string('name', 200);
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('line_total', 12, 2)->default(0);
            });
        }

        if (! Schema::hasTable('invoice_payment')) {
            Schema::create('invoice_payment', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('payment_id')->index();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('method', 60)->nullable();
                $table->string('reference', 120)->nullable();
                $table->string('paid_at', 100)->nullable();
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
                    $lengths = ['category_amount' => 1000, 'category_name' => 1000, 'remarks' => 500];
                    $blueprint->string($column, $lengths[$column] ?? 100)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payment');
        Schema::dropIfExists('invoice_item');
    }
};
