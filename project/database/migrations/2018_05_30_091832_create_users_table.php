<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('username')->nullable();
			$table->string('first_name', 191);
			$table->string('last_name', 191)->nullable();
			$table->string('email', 191)->unique();
			$table->string('password', 191);
			$table->date('date_of_birth');
			$table->enum('gender', array('M','F'))->default('M');
			$table->string('location')->nullable();
			$table->string('latitude', 200);
			$table->string('longitude', 200);
			$table->string('profile_picture')->nullable()->default('NA');
			$table->string('profile_picture_small')->nullable();
			$table->string('cover_picture')->nullable()->default('NA');
			$table->string('cover_picture_small')->nullable();
			$table->text('about_me')->nullable();
			$table->string('phone', 100)->nullable();
			$table->boolean('privacy_scope')->nullable()->default(1)->comment('1= Only Me, 2= Friends, 3= Public');
			$table->enum('is_terms_accepted', array('Y','N'))->default('N');
			$table->enum('is_email_verified', array('Y','N'))->default('N');
			$table->string('email_verification_token')->nullable();
			$table->string('reset_verification_token')->nullable();
			$table->string('api_token', 191);
			$table->string('remember_token', 100)->nullable();
			$table->integer('login_type')->default(1)->comment('1= Normal, 2= Facebook, 3= Google');
			$table->enum('is_active', array('Y','N'))->default('N');
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
		Schema::drop('users');
	}

}
