<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bankb', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group', 100);
            $table->string('status', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bankb');
    }
};
