<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_material', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date', 100);
            $table->string('title', 100);
            $table->string('category', 100);
            $table->string('patient', 100);
            $table->string('patient_name', 100);
            $table->string('patient_address', 100);
            $table->string('patient_phone', 100);
            $table->string('url', 1000);
            $table->string('date_string', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_material');
    }
};
