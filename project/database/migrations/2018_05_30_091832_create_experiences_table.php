<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExperiencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('experiences', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->string('designation');
			$table->string('company_name');
			$table->string('location')->nullable();
			$table->string('latitude', 200);
			$table->string('longitude', 200);
			$table->integer('from_month');
			$table->integer('from_year');
			$table->boolean('is_currently_working')->default(1)->comment('0=Not Working, 1=Working currently in this company');
			$table->integer('to_month')->nullable();
			$table->integer('to_year')->nullable();
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
		Schema::drop('experiences');
	}

}
