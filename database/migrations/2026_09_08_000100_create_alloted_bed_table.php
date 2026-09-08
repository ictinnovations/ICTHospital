<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alloted_bed', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 100);
            $table->string('category', 100);
            $table->string('patient', 100);
            $table->string('a_time', 100);
            $table->string('d_time', 100);
            $table->string('status', 100);
            $table->string('x', 100);
            $table->string('bed_id', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alloted_bed');
    }
};
