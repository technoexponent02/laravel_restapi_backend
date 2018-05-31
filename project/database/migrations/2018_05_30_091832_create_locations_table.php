<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('locations', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('locationable_id');
			$table->string('locationable_type');
			$table->integer('region_id')->index('region_id');
			$table->timestamps();
			$table->unique(['locationable_type','locationable_id','region_id'], 'locationable_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('locations');
	}

}
