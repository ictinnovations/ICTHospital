<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patient', 100);
            $table->string('doctor', 100);
            $table->string('date', 100);
            $table->string('time_slot', 100);
            $table->string('s_time', 100);
            $table->string('e_time', 100);
            $table->string('remarks', 500);
            $table->string('add_date', 100);
            $table->string('registration_time', 100);
            $table->string('s_time_key', 100);
            $table->string('status', 100);
            $table->string('user', 100);
            $table->string('request', 100);
            $table->string('b_p', 100)->nullable();
            $table->string('pulse', 100)->nullable();
            $table->string('temprature', 100)->nullable();
            $table->string('weight', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment');
    }
};
