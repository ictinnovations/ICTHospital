<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFamilyVoucharTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('family_vouchar', function (Blueprint $table) {
                $table->increments('id');
                $table->string('family_id',50);
                $table->string('bills');
                $table->date('date',150);
                $table->string('status',10);
                $table->decimal('amount',18,2);
                $table->decimal('dueamount',18,2);
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
        Schema::dropIfExists('family_vouchar');
    }
}
