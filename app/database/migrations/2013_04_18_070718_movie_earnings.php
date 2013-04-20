<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MovieEarnings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('movie_earnings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('movie_id')->unsigned();
			$table->date('date');
			$table->integer('domestic')->unsigned(); // Fun factoid: Avatar worldwide gross is already larger than the limits for signed int
			$table->timestamps();

			$table->unique(array('movie_id', 'date'));
			$table->foreign('movie_id')->references('id')->on('movies')->onUpdate('cascade')->onDelete('cascade');
		});
		Schema::table('movies', function(Blueprint $table) {
			$table->integer('latest_earnings_id')->unsigned()->nullable()->after("release");

			$table->foreign('latest_earnings_id')->references('id')->on('movie_earnings')->onUpdate('cascade')->onDelete('set null');
		});
		Schema::table('league_user', function(Blueprint $table) {
			$table->integer('earnings_total')->unsigned()->after('player');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('league_user', function(Blueprint $table) {
			$table->dropColumn('earnings_total');
		});
		Schema::table('movies', function(Blueprint $table) {
			$table->dropForeign('movies_latest_earnings_id_foreign');
			$table->dropColumn('latest_earnings_id');
		});
		Schema::drop('movie_earnings');
	}

}
