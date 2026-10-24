<?php
/**
 * ICTHospital - who took each payment, and the end-of-shift cash close.
 *
 * At the end of a shift the cashier counts the drawer and hands over. The system
 * has to say what should be there: every payment that cashier took since their last
 * close, split by how it was paid. A payment belongs to exactly one close, so money
 * cannot be counted twice or slip between two shifts.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_payment', function (Blueprint $table) {
            $table->unsignedInteger('received_by')->nullable()->index();
            $table->unsignedInteger('shift_close_id')->nullable()->index();
        });

        Schema::create('shift_close', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closed_at');
            $table->unsignedInteger('payments_count')->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('cash', 12, 2)->default(0);
            $table->decimal('bank_transfer', 12, 2)->default(0);
            $table->decimal('card', 12, 2)->default(0);
            $table->decimal('cheque', 12, 2)->default(0);
            $table->decimal('other', 12, 2)->default(0);
            $table->decimal('counted_cash', 12, 2)->nullable();
            $table->decimal('difference', 12, 2)->nullable();
            $table->string('comment', 500)->nullable();
            $table->unsignedInteger('closed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_close');
        Schema::table('invoice_payment', fn (Blueprint $t) => $t->dropColumn(['received_by', 'shift_close_id']));
    }
};
