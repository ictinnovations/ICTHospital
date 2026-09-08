<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accountant', function (Blueprint $table) {
            $table->increments('id');
            $table->string('img_url', 200);
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('address', 100);
            $table->string('phone', 100);
            $table->string('x', 100);
            $table->string('ion_user_id', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accountant');
    }
};
