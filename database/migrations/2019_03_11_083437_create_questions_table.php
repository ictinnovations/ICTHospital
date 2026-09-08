<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            //$table->integer('section_id')->unsigned();
            $table->string('class_code',50);
            $table->integer('subject_id');
            $table->string('chapter',150);
            $table->string('session',15);
            $table->string('question_name',180);
            $table->integer('question_type');
            $table->string('choices',255)->nullable();
            $table->string('answer',255)->nullable();
            $table->integer('points')->default(1);
            $table->string('type')->nullable();
            $table->string('level')->nullable();
            $table->string('logo')->nullable();
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
        Schema::dropIfExists('questions');
    }
}
