<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient', function (Blueprint $table) {
            $table->increments('id');
            $table->string('img_url', 100);
            $table->string('name', 100);
            $table->string('email', 1000);
            $table->string('doctor', 100);
            $table->string('address', 100);
            $table->string('phone', 100);
            $table->string('sex', 100);
            $table->string('birthdate', 100);
            $table->string('age', 100);
            $table->string('bloodgroup', 100);
            $table->string('ion_user_id', 100);
            $table->string('patient_id', 100);
            $table->string('add_date', 100);
            $table->string('registration_time', 100);
            $table->string('how_added', 100);
            $table->string('father_name', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient');
    }
};
