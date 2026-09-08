<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_expense_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category', 100);
            $table->string('description', 100);
            $table->string('x', 100);
            $table->string('y', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_expense_category');
    }
};
