<?php
class UpdateUserEarnings {

	public function fire($job, $data) {
		$league_player = League::find($data["league_id"])->players()->where("user_id", $data["user_id"])->first();
		if(!$league_player) {
			Log::info("UpdateUserEarnings: League Player not found", $data);
			$job->release(5);
			return;
		}
		// Check latest updated timestamp, helps avoid duplicates
		if(new DateTime($league_player->pivot->updated_at) >= new DateTime("@".$data["since"])) {
			$job->delete();
			return;
		}
		// Get total earnings
		$totals = DB::table("league_movie_user")
			->join("movies", "movies.id", "=", "league_movie_user.movie_id")
			->join("movie_earnings", "movie_earnings.id", "=", "movies.latest_earnings_id")
			->where("league_movie_user.league_id", $data["league_id"])
			->where("league_movie_user.user_id", $data["user_id"])
			->pluck(DB::raw("SUM(movie_earnings.domestic)"));

		$league_player->pivot->earnings_total = $totals;
		$league_player->pivot->save();

		$job->delete();
	}
}