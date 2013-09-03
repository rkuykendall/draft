<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrackEarningsPerLeague extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('league_movie', function(Blueprint $table)
		{
			$table->integer('latest_earnings_id')->unsigned()->nullable()->after("price");

			$table->foreign('latest_earnings_id')->references('id')->on('movie_earnings')->onUpdate('cascade')->onDelete('set null');
		});
		// Slow migration, but this is one time so CBA optimising
		foreach (League::all() as $league) {
			foreach ($league->movies as $movie) {
				$movie->pivot->latest_earnings_id = $movie->latest_earnings_id;
				$movie->pivot->save();
			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('league_movie', function(Blueprint $table)
		{
			$table->dropForeign('league_movie_latest_earnings_id_foreign');
			$table->dropColumn('latest_earnings_id');
		});
	}

}