<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 500);
            $table->string('description', 100);
            $table->string('date', 100);
            $table->string('type', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice');
    }
};
