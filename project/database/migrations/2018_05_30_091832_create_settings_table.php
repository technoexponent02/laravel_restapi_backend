<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('admin_name');
			$table->string('admin_email');
			$table->string('site_title');
			$table->string('contact_email');
			$table->string('contact_name');
			$table->string('contact_phone');
			$table->string('paypal_email')->nullable();
			$table->string('site_logo');
			$table->string('site_fb_link');
			$table->string('site_twitter_link');
			$table->string('site_gplus_link');
			$table->string('site_linkedin_link');
			$table->string('site_pinterest_link');
			$table->string('no_of_min_coin_balance_for_withdraw', 10);
			$table->float('minimum_withdraw_amount', 11);
			$table->float('site_commission_add_to_wallet', 10, 0);
			$table->integer('no_of_days_for_solve_dispute');
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
		Schema::drop('settings');
	}

}
