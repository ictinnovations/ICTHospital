<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject', 100);
            $table->string('date', 100);
            $table->string('message', 10000);
            $table->string('reciepient', 100);
            $table->string('user', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email');
    }
};
