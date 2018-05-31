<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('post_description');
			$table->string('location');
			$table->string('latitude', 200);
			$table->string('longitude', 200);
			$table->string('post_type');
			$table->integer('post_visibility')->default(0)->comment('0= Public, 1=Private');
			$table->integer('user_id')->unsigned()->index('user_id');
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
		Schema::drop('posts');
	}

}
