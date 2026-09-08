<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patient_id', 200);
            $table->string('membership_type', 100);
            $table->string('date', 100);
            $table->string('expiry', 100);
            $table->string('status', 100);
            $table->string('ion_user_id', 100);
            $table->timestamp('created_at');
            $table->string('card_no', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership');
    }
};
