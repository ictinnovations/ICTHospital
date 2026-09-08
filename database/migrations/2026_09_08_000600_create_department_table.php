<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('description', 1000);
            $table->string('x', 10);
            $table->string('y', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department');
    }
};
