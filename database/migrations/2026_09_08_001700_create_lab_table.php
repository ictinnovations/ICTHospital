<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category', 100);
            $table->string('patient', 100);
            $table->string('doctor', 100);
            $table->string('date', 100);
            $table->string('category_name', 1000);
            $table->string('report', 10000);
            $table->string('status', 100);
            $table->string('user', 100);
            $table->string('patient_name', 100);
            $table->string('patient_phone', 100);
            $table->string('patient_address', 100);
            $table->string('doctor_name', 100);
            $table->string('date_string', 100);
            $table->string('invoice_id', 100)->nullable();
            $table->string('report_date', 100)->nullable();
            $table->string('cnic', 100)->nullable();
            $table->string('token_no', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab');
    }
};
