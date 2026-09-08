<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service', function (Blueprint $table) {
            $table->increments('id');
            $table->string('img_url', 1000);
            $table->string('title', 100);
            $table->string('description', 1000);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};
