<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category', 100);
            $table->string('patient', 100);
            $table->string('doctor', 100);
            $table->string('date', 100);
            $table->string('amount', 100);
            $table->string('vat', 100)->default('0');
            $table->string('x_ray', 100);
            $table->string('flat_vat', 100);
            $table->string('discount', 100)->default('0');
            $table->string('flat_discount', 100);
            $table->string('gross_total', 100);
            $table->string('remarks', 500);
            $table->string('hospital_amount', 100);
            $table->string('doctor_amount', 100);
            $table->string('category_amount', 1000);
            $table->string('category_name', 1000);
            $table->string('amount_received', 100);
            $table->string('deposit_type', 100);
            $table->string('status', 100);
            $table->string('user', 100);
            $table->string('patient_name', 100);
            $table->string('patient_phone', 100);
            $table->string('patient_address', 100);
            $table->string('doctor_name', 100);
            $table->string('date_string', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
