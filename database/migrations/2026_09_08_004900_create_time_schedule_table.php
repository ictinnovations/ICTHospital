<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doctor', 500);
            $table->string('weekday', 100);
            $table->string('s_time', 100);
            $table->string('e_time', 100);
            $table->string('s_time_key', 100);
            $table->string('duration', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_schedule');
    }
};
