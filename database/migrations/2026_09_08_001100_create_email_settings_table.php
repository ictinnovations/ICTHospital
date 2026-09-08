<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('admin_email', 100);
            $table->string('type', 100);
            $table->string('user', 100);
            $table->string('password', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
