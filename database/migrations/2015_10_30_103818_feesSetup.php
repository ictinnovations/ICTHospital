<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FeesSetup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feesSetup', function(Blueprint $table)
		{
			$table->id();
            $table->string('class', 20);
            $table->string('type', 20);
            $table->string('title', 100);
            $table->decimal('fee', 18, 2);
            $table->decimal('Latefee', 18, 2)->default(0);
            $table->text('description')->nullable();
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
		  Schema::drop('feesSetup');
	}

}
