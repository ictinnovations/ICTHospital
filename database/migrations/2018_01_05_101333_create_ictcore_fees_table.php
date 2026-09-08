<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIctcoreFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ictcore_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',30);
	    $table->text('description');
            $table->string('recording',255);
            $table->string('ictcore_recording_id',30);
            $table->string('ictcore_program_id',30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ictcore_fees');
    }
}
