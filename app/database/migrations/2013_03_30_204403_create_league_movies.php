<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeagueMovies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('league_movie', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('league_id')->unsigned();
			$table->integer('movie_id')->unsigned();
			$table->integer('price')->default(0);
			$table->timestamps();

			$table->foreign('league_id')->references('id')->on('leagues')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('movie_id')->references('id')->on('movies')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('league_movie');
	}

}