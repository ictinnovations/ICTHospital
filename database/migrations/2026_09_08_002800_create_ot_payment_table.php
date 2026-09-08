<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ot_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patient', 100);
            $table->string('doctor_c_s', 100);
            $table->string('doctor_a_s_1', 100);
            $table->string('doctor_a_s_2', 100);
            $table->string('doctor_anaes', 100);
            $table->string('n_o_o', 100);
            $table->string('c_s_f', 100);
            $table->string('a_s_f_1', 100);
            $table->string('a_s_f_2', 11);
            $table->string('anaes_f', 100);
            $table->string('ot_charge', 100);
            $table->string('cab_rent', 100);
            $table->string('seat_rent', 100);
            $table->string('others', 100);
            $table->string('discount', 100);
            $table->string('date', 100);
            $table->string('amount', 100);
            $table->string('doctor_fees', 100);
            $table->string('hospital_fees', 100);
            $table->string('gross_total', 100);
            $table->string('flat_discount', 100);
            $table->string('amount_received', 100);
            $table->string('status', 100);
            $table->string('user', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ot_payment');
    }
};
