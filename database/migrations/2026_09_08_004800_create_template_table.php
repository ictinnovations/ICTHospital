<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('template', 10000);
            $table->string('user', 100);
            $table->string('x', 100);
            $table->string('procedure_id', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template');
    }
};
