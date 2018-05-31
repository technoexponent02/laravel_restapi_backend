<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaggedTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tagged_tags', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('tagable_id');
			$table->string('tagable_type');
			$table->integer('tag_id')->index('tag_id');
			$table->timestamps();
			$table->unique(['tagable_type','tagable_id','tag_id'], 'tagable_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tagged_tags');
	}

}
