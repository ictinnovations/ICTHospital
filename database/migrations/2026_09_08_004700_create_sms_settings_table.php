<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('username', 100);
            $table->string('password', 100);
            $table->string('api_id', 100);
            $table->string('sender', 100);
            $table->string('authkey', 100);
            $table->string('user', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
