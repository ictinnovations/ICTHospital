<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slide', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->string('img_url', 1000);
            $table->string('text1', 500);
            $table->string('text2', 500);
            $table->string('text3', 500);
            $table->string('position', 100);
            $table->string('status', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slide');
    }
};
