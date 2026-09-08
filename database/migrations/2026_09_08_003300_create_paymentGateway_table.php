<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paymentGateway', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('merchant_key', 100);
            $table->string('salt', 100);
            $table->string('x', 100);
            $table->string('y', 100);
            $table->string('APIUsername', 100);
            $table->string('APIPassword', 100);
            $table->string('APISignature', 100);
            $table->string('status', 1000);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paymentGateway');
    }
};
