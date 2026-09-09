<?php
/**
 * ICTHospital - the accounting_settings table.
 *
 * AccountingSetting has always been in the model directory and the accounting
 * page has always queried it, but no migration ever created the table, so
 * /accounting was a server error on any fresh install. This adds it.
 *
 * It holds the credentials for an external accounting API, one row.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accounting_settings')) {
            return;
        }

        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_id', 100)->nullable();
            $table->string('api_link', 255)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('password', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};
