<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('group', 10);
            $table->string('age', 10);
            $table->string('sex', 10);
            $table->string('ldd', 100);
            $table->string('phone', 100);
            $table->string('email', 100);
            $table->string('add_date', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor');
    }
};
