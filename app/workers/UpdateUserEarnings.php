<?php
class UpdateUserEarnings {

	public function fire($job, $data) {
		$league = League::find($data['league_id']);
		$league_player = $league->players()->where('user_id', $data['user_id'])->first();
		if(!$league_player) {
			Log::info('UpdateUserEarnings: League Player not found', $data);
			$job->release(5);
			return;
		}
		// Check latest updated timestamp, helps avoid duplicates
		if(new DateTime($league_player->pivot->updated_at) >= new DateTime('@'.$data['since'])) {
			$job->delete();
			return;
		}
		// Get total earnings
		$totals = DB::table('league_movie_user')
			->join('league_movie', function ($join) {
				$join->on('league_movie.movie_id', '=', 'league_movie_user.movie_id')
				     ->on('league_movie.league_id', '=', 'league_movie_user.league_id');
			})
			->join('movie_earnings', 'movie_earnings.id', '=', 'league_movie.latest_earnings_id')
			->where('league_movie_user.league_id', $data['league_id'])
			->where('league_movie_user.user_id', $data['user_id'])
			->pluck(DB::raw('SUM(movie_earnings.domestic)'));

		$league_player->pivot->earnings_total = $totals;
		$league_player->pivot->save();

		$job->delete();
	}
}