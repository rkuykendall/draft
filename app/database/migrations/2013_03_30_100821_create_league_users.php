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
			$table->boolean('admin')->default(false);
			$table->timestamps();

			$table->foreign('league_id')->references('id')->on('leagues')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
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