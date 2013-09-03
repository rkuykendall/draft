<?php
class UpdateEarnings {

	/**
	 * The base box office mojo url
	 *
	 * @var string
	 */
	protected $baseURL = "http://boxofficemojo.com/movies/?id=";


	public function fire($job, $data) {
		if ($job->attempts() > 3) {
			return $job->delete();
		}

		$movie = Movie::find($data["movie_id"]);
		if(!$movie) {
			$job->release(5);
			return;
		}
		$earnings = $movie->earnings()->where('date', (new Carbon())->tz("America/Los_Angeles")->format("Y-m-d"))->first();

		if(!$earnings) {
			$earnings = new MovieEarning(array("movie_id" => $movie->id, "date" => (new Carbon())->tz("America/Los_Angeles")));
		}

		$money = $this->liberateBOE($movie->boxmojo_id);
		if($money == 0) {
			$job->delete();
			return;
		}
		$earnings->domestic = $money;

		$earnings->save();
		if(!$movie->latestEarnings or $earnings->date > $movie->latestEarnings->date) {
			$movie->latest_earnings_id = $earnings->id;
			$movie->save();
			// Update latest earnings id for active leagues too
			DB::table('league_movie')
			  ->join('leagues', 'league_movie.league_id', '=', 'leagues.id')
			  ->where('league_movie.movie_id', $movie->id)
			  ->where('leagues.end_date', '>=', new DateTime())
			  ->update(array('league_movie.latest_earnings_id' => $movie->latest_earnings_id));
		}

		// Queue up league user updates
		// Update only leagues that are active
		$users = $movie->users()
		               ->join('leagues', 'league_movie_user.league_id', '=', 'leagues.id')
		               ->where('leagues.end_date', '>=', new DateTime()) // Inner join filters out all non-valid movies
		               ->get();

		foreach ($users as $user) {
			Queue::push("UpdateUserEarnings", array(
				"user_id" => $user->id, "league_id" => $user->pivot->league_id, "since" => $earnings->updated_at->format('U')
			));
		}
		$job->delete();
	}

	/**
	*  Returns the latest BOE for the movie
	*
	*  @return int
	*/
	private function liberateBOE($mojoId) {
		// Using queue delays instead, sleep left in for local/noqueue systems
		usleep(rand(0,1000000)); //hopefully this will help keep us on the dl

		$htmlString = file_get_contents($this->baseURL.$mojoId.'.htm');
		$matches;
		$found = preg_match('/Domestic Total as of [\w\.]+\s*\d+,?\s*\d*:<\/font>\s*<b>\$([0-9,]*)/', $htmlString, $matches);

		if(!$found) { // Guess movies get archived at some point on BOE
			$found = preg_match('/Domestic Total Gross:\s*<b>\$([0-9,]*)/', $htmlString, $matches);
		}

		$BOE = $found ? intval(str_replace(',', '', $matches[1])) : 0;

		return $BOE;
	}
}