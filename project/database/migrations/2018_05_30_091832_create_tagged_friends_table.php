<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaggedFriendsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tagged_friends', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('tag_friendable_id');
			$table->string('tag_friendable_type');
			$table->integer('user_id')->index('user_id');
			$table->timestamps();
			$table->unique(['tag_friendable_type','tag_friendable_id','user_id'], 'tagable_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tagged_friends');
	}

}
