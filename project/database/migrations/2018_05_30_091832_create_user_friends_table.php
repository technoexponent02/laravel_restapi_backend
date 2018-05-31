<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserFriendsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_friends', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('friend_id')->unsigned()->index('friend_id');
			$table->boolean('friend_link_id')->default(0)->comment('0-request record, primary key be used for friend_link_id on parent relation creation');
			$table->boolean('is_requested')->default(3)->comment('0-got request, 1-send request, 3-NA');
			$table->boolean('is_accepted')->default(0)->comment('0-Not a friend, 1-accepted request, 3-Pending decision');
			$table->boolean('is_blocked')->default(0)->comment('0-Not blocked, 1- blocked');
			$table->integer('is_followed')->default(0)->comment('/*   0-Not followed, 1- followed  */');
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
		Schema::drop('user_friends');
	}

}
