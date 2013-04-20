<?php
class UpdateEarnings {

	/**
	 * The base box office mojo url
	 *
	 * @var string
	 */
	protected $baseURL = "http://boxofficemojo.com/movies/?id=";


	public function fire($job, $data) {
		DB::listen(function($sql, $bindings, $time)
		{
		    s($sql, $bindings, $time);
		});

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
			foreach ($movie->users as $user) {
				Queue::push("UpdateUserEarnings", array(
					"user_id" => $user->id, "league_id" => $user->pivot->league_id, "since" => $earnings->updated_at->format('U')
				));
			}
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

		$doc = new DOMDocument();

		//Wrap the loading function with a silent error wrapper as the site is not properly formed
		try {
			$doc->loadHTMLFile( $this->baseURL.$mojoId.'.htm' );
		} catch(ErrorException $e) {}

		/*
		*  Yes, nothing like parsing html with regex :(  unfortunately the mark up is too f'd for the parser.
		*/
		$htmlString = $doc->saveHTML();
		$matches;
		$found = preg_match('/Domestic Total as of [\w\.]+\s*\d+,?\s*\d*:<\/font>\s*<b>\$([0-9,]*)/', $htmlString, $matches);

		$BOE = $found ? intval(str_replace(',', '', $matches[1])) : 0;

		return $BOE;
	}
}