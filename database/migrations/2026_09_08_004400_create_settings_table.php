<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system_vendor', 100);
            $table->string('title', 100);
            $table->string('address', 100);
            $table->string('phone', 100);
            $table->string('email', 100);
            $table->string('facebook_id', 100);
            $table->string('currency', 100);
            $table->string('language', 100);
            $table->string('discount', 100);
            $table->string('vat', 100);
            $table->string('login_title', 100);
            $table->string('logo', 500);
            $table->string('invoice_logo', 500);
            $table->string('payment_gateway', 100);
            $table->string('sms_gateway', 100);
            $table->string('codec_username', 100);
            $table->string('codec_purchase_code', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
