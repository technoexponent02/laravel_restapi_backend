<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLikesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('likes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('likeable_id');
			$table->string('likeable_type');
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->timestamps();
			$table->unique(['likeable_type','likeable_id','id'], 'linked_imageable_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('likes');
	}

}
