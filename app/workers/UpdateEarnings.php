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
		$earnings = $movie->earnings()->where('date', '=', (new DateTime())->format("Y-m-d"))->first();

		if(!$earnings) {
			$earnings = new MovieEarning(array("movie_id" => $movie->id, "date" => new DateTime()));
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
			// Queue up league user updates
		}

		foreach ($movie->users as $user) {
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
		sleep(rand(1,8)); //hopefully this will help keep us on the dl

		$htmlString = file_get_contents($this->baseURL.$mojoId.'.htm');
		$matches;
		$found = preg_match('/Domestic Total as of [\w\.]+\s*\d+,?\s*\d*:<\/font>\s*<b>\$([0-9,]*)/', $htmlString, $matches);

		$BOE = $found ? intval(str_replace(',', '', $matches[1])) : 0;

		return $BOE;
	}
}