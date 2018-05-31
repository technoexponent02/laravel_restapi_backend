<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserFriendsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_friends', function(Blueprint $table)
		{
			$table->foreign('user_id', 'user_friends_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('friend_id', 'user_friends_ibfk_2')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_friends', function(Blueprint $table)
		{
			$table->dropForeign('user_friends_ibfk_1');
			$table->dropForeign('user_friends_ibfk_2');
		});
	}

}
