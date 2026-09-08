<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category', 100);
            $table->string('description', 100);
            $table->string('c_price', 100);
            $table->string('type', 100);
            $table->integer('d_commission');
            $table->integer('h_commission');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_category');
    }
};
