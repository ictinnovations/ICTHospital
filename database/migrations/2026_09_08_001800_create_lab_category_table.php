<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category', 100);
            $table->string('description', 100);
            $table->string('reference_value', 1000);
            $table->string('procedure_id', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_category');
    }
};
