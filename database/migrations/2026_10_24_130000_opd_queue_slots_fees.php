<?php
/**
 * ICTHospital - OPD token queue, doctor booking slots and visit fees.
 *
 * An outpatient clinic runs on tokens: the patient checks in at the desk, gets the
 * next number for that doctor, waits, is called in and leaves. The waiting time and
 * the time with the doctor are the two numbers a clinic manager watches, and they
 * fall out of three timestamps.
 *
 * Slots say when a doctor sees patients, so bookings cannot land outside clinic
 * hours or on a day the doctor is away. Visit fees say what a first visit, a
 * follow-up or an emergency visit costs with each doctor.
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
        Schema::table('appointment', function (Blueprint $table) {
            $table->unsignedInteger('token_no')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('consult_started_at')->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->string('visit_type', 20)->nullable();
            // The invoice raised for this visit's fee, so it is not billed twice.
            $table->unsignedInteger('payment_id')->nullable();
        });

        Schema::create('doctor_slot', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('doctor_id')->index();
            $table->unsignedTinyInteger('weekday'); // 0 Sunday .. 6 Saturday
            $table->string('start_time', 5);
            $table->string('end_time', 5);
            $table->unsignedSmallInteger('slot_minutes')->default(15);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('slot_block', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('doctor_id')->index();
            $table->date('date');
            // Both null blocks the whole day.
            $table->string('start_time', 5)->nullable();
            $table->string('end_time', 5)->nullable();
            $table->string('reason', 120)->nullable();
            $table->timestamps();
        });

        Schema::create('visit_fee', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('doctor_id')->nullable(); // null: every doctor
            $table->string('visit_type', 20); // first, follow_up, emergency
            $table->string('shift', 10)->default('any'); // any, morning, evening
            $table->unsignedInteger('payment_category_id');
            // Null: take the price from the price list for the patient's type.
            $table->decimal('fee', 12, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_fee');
        Schema::dropIfExists('slot_block');
        Schema::dropIfExists('doctor_slot');
        Schema::table('appointment', fn (Blueprint $t) => $t->dropColumn([
            'token_no', 'checked_in_at', 'consult_started_at', 'checked_out_at', 'visit_type', 'payment_id',
        ]));
    }
};
