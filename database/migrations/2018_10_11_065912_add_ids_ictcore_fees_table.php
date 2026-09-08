<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdsIctcoreFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ictcore_attendance', function (Blueprint $table) {
            $table->string('ictcore_recording_id_late',255)->nullable();
            $table->string('ictcore_program_id_late',255)->nullable();
            $table->string('telenor_file_id_late',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ictcore_attendance', function (Blueprint $table) {
            //
        });
    }
}
