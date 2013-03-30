<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeagueUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('league_user', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('league_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->boolean('player')->default(true);
			$table->boolean('admin');
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
		Schema::drop('league_user');
	}

}