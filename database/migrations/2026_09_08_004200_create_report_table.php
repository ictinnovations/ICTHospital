<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report', function (Blueprint $table) {
            $table->increments('id');
            $table->string('report_type', 100);
            $table->string('patient', 100);
            $table->string('description', 500);
            $table->string('doctor', 100);
            $table->string('date', 100);
            $table->string('add_date', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
