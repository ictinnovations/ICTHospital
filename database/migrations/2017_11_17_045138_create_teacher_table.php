<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName',60);
	    $table->string('lastName',60);
	    $table->string('gender',10)->nullable();
	    $table->string('religion',15)->nullable();
	    $table->string('bloodgroup',10)->nullable();
	    $table->string('nationality',50)->nullable();
	    $table->string('dob',12)->nullable();
	    $table->string('photo',30)->nullable();
	    $table->string('phone',150);
	    $table->string('email',250)->nullable();
	    $table->string('fatherName',180);
	    $table->string('fatherCellNo',15);
             $table->string('presentAddress',500);
 	     $table->string('parmanentAddress',500)->nullable();
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
        Schema::dropIfExists('teacher');
    }
}
