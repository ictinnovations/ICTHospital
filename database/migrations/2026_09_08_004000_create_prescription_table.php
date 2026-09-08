<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date', 100);
            $table->string('patient', 100);
            $table->string('doctor', 100);
            $table->string('symptom', 100);
            $table->string('advice', 1000);
            $table->string('state', 100);
            $table->string('dd', 100);
            $table->string('medicine', 1000);
            $table->string('validity', 100);
            $table->string('note', 1000);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription');
    }
};
