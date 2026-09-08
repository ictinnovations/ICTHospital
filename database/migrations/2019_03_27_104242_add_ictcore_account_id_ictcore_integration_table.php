<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIctcoreAccountIdIctcoreIntegrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ictcore_integration', function (Blueprint $table) {
            $table->integer('ictcore_account_id')->after('ictcore_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ictcore_integration', function (Blueprint $table) {
            //
        });
    }
}
