<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherhistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucherhistories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amount');
            $table->integer('ref_id');
            $table->string('rgiNo',100);
            $table->string('status',50);
            $table->string('type',50);
            $table->date('date');
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
        Schema::dropIfExists('voucherhistories');
    }
}
