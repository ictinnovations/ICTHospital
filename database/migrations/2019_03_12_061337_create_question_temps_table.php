<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_temps', function (Blueprint $table) {
            $table->increments('id');

            $table->string('class_code',50);
            $table->integer('subject_id');
            $table->string('chapter',150);
            $table->string('session',15);
            $table->string('quize_name',100);
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
        Schema::dropIfExists('question_temps');
    }
}
