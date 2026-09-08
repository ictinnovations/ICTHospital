<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patient_id', 100);
            $table->string('title', 100);
            $table->string('description', 10000);
            $table->string('patient_name', 100);
            $table->string('patient_address', 500);
            $table->string('patient_phone', 100);
            $table->string('img_url', 500);
            $table->string('date', 100);
            $table->string('registration_time', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_history');
    }
};
