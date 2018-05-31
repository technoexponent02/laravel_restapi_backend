<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('commentable_id');
			$table->string('commentable_type');
			$table->text('comment');
			$table->integer('parent_id')->unsigned()->default(0);
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->timestamps();
			$table->unique(['commentable_type','commentable_id','id'], 'linked_commentable_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comments');
	}

}
