<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('membership_name', 200);
            $table->string('membership_price', 100);
            $table->string('expiry', 100);
            $table->timestamp('created_at');
            $table->string('discount', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_settings');
    }
};
