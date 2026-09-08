<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_deposit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patient', 100);
            $table->string('payment_id', 100);
            $table->string('date', 100);
            $table->string('deposited_amount', 100);
            $table->string('amount_received_id', 100);
            $table->string('deposit_type', 100);
            $table->string('gateway', 100);
            $table->string('user', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_deposit');
    }
};
