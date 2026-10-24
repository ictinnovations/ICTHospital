<?php
/**
 * ICTHospital - doctor share.
 *
 * Private hospitals pay most doctors a cut of what they bill: a percentage of each
 * visit or procedure, sometimes different for panel patients, sometimes a smaller
 * cut for the doctor who referred the patient. The legacy d_commission column held
 * one number per service and nothing ever applied it.
 *
 * A policy is a set of rules. A doctor is assigned one policy. Every invoice line is
 * matched to the most specific rule and the share is written as a row, so what a
 * doctor is owed is a list of lines that can be approved and paid, not a sum
 * somebody works out at the end of the month.
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
        Schema::create('share_policy', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('notes', 255)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('share_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('share_policy_id')->index();
            // Each of these narrows the rule. Null means "any".
            $table->unsignedInteger('payment_category_id')->nullable();
            $table->string('department', 100)->nullable();
            $table->unsignedInteger('patient_type_id')->nullable();
            $table->decimal('doctor_percent', 5, 2)->default(0);
            $table->decimal('referral_percent', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('doctor', function (Blueprint $table) {
            $table->unsignedInteger('share_policy_id')->nullable();
        });

        Schema::table('payment', function (Blueprint $table) {
            $table->unsignedInteger('referring_doctor')->nullable();
        });

        Schema::table('invoice_item', function (Blueprint $table) {
            // The doctor who did this line, when not the invoice's doctor.
            $table->unsignedInteger('doctor_id')->nullable();
        });

        Schema::create('doctor_share', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_id')->index();
            $table->unsignedInteger('invoice_item_id')->index();
            $table->unsignedInteger('doctor_id')->index();
            $table->string('role', 12); // performing or referring
            $table->unsignedInteger('share_rule_id')->nullable();
            $table->decimal('base_amount', 12, 2);
            $table->decimal('percent', 5, 2);
            $table->decimal('amount', 12, 2);
            $table->string('status', 10)->default('pending'); // pending, approved, paid
            $table->unsignedInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('payout_ref', 60)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_share');
        Schema::table('invoice_item', fn (Blueprint $t) => $t->dropColumn('doctor_id'));
        Schema::table('payment', fn (Blueprint $t) => $t->dropColumn('referring_doctor'));
        Schema::table('doctor', fn (Blueprint $t) => $t->dropColumn('share_policy_id'));
        Schema::dropIfExists('share_rule');
        Schema::dropIfExists('share_policy');
    }
};
