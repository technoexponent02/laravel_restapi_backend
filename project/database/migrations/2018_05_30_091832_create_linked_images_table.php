<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLinkedImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('linked_images', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('linked_imageable_id');
			$table->string('linked_imageable_type');
			$table->string('actual_image');
			$table->string('pixelated_image');
			$table->timestamps();
			$table->unique(['linked_imageable_type','linked_imageable_id','id'], 'linked_imageable_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('linked_images');
	}

}
