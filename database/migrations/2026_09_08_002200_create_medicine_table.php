<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('category', 100);
            $table->string('price', 100);
            $table->string('box', 100);
            $table->string('s_price', 100);
            $table->integer('quantity');
            $table->string('generic', 100);
            $table->string('company', 100);
            $table->string('effects', 100);
            $table->string('e_date', 70);
            $table->string('add_date', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine');
    }
};
