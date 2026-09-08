<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bed', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category', 100);
            $table->string('number', 100);
            $table->string('description', 100);
            $table->string('last_a_time', 100);
            $table->string('last_d_time', 100);
            $table->string('status', 100);
            $table->string('bed_id', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bed');
    }
};
