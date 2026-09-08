<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostic_report', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date', 100);
            $table->string('invoice', 100);
            $table->string('report', 10000);
            $table->string('status', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_report');
    }
};
