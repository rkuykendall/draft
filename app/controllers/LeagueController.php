<?php

class LeagueController extends BaseController {
	public $layout = 'layout.main';

	/* ROUTES */

	/* League list */
	public function getList() {
		$leagues = League::wherePrivate(false)->get();
		$this->layout->title = "Leagues";
		$this->layout->content = View::make("league.list", array(
			"leagues" => $leagues
		));
	}

	/* Create League */
	public $league_create_valid_rules = array(
		"name" => "required|max:255",
		"description" => "required",
		"url" => "url",

		"money" => "required|integer",
		"units" => "required|max:16",
	);

	public function getCreate() {
		$this->layout->title = "New League";
		$this->layout->content = View::make("league.create", array(
			"create_rules" => $this->league_create_valid_rules
		));
	}
	public function postCreate() {
		$validator = Validator::make(Input::all(), $this->league_create_valid_rules);
		if($validator->fails()) {
			Notification::error("Duder something is wrong with your input.");
			return Redirect::action("LeagueController@getCreate")->withInput()->withErrors($validator);
		}

		$league = new League();
		$league->name        = Input::get("name");
		$league->description = Input::get("description");
		$league->url         = Input::get("url");
		$league->private     = Input::get("private") ? true : false;

		$league->mode        = Config::get("draft.league_defaults.mode");
		$league->money       = Input::get("money");
		$league->units       = Input::get("units");
		$league->end_date    = "2013-09-20";
		if($league->save()) {
			// Add user as admin
			$league->users()->attach(Auth::user(), array("player" => 0, "admin" => 1));
			// Add movies
			//ALPHA: Default set
			$predefined = Movie::whereIn('id', Config::get("draft.league_defaults.movies"))->get();
			$syncable = array();
			foreach ($predefined as $movie) {
				$syncable[$movie->id] = array('latest_earnings_id' => $movie->latest_earnings_id, 'price' => 0);
			}
			$league->movies()->sync($syncable);
			Notification::success("Good Luck Have Batman");
			return Redirect::action("LeagueController@getView", array("league_slug" => $league->slug));
		} else {
			Notification::error("Whoops, the database took a dump. Try again later maybe?");
			return Redirect::action("LeagueController@getCreate")->withInput();
		}
	}

	/* League views */
	public function getView(League $league) {
		$league->load(array(
			'movies',
			'movies.latestEarnings',
			'players',
			'players.movies' => function ($query) use ($league) {
				$query->where('league_id', $league->id);
			}
		));
		// Link up draft-movie pivot
		$league->players->each(function($player) use($league) {
			$player->movies->map(function($movie) use($league) {
				$lmovie = $league->movies->find($movie->id);
				if($lmovie) {
					$movie->grabLeaguePivot($lmovie);
				}
			});
			$player->buytotal = array_sum($player->movies->map(function($movie) {
				return $movie->league_pivot->price;
			})->toArray());
		});

		$this->layout->title = $league->name;
		$this->layout->content = View::make("league.view", array(
			'league' => $league,
		));
	}
	public function getViewPlayers(League $league) {
		// Load extra data
		$league->load(array(
			'users',
			'movies',
			'movies.latestEarnings',
			'players.movies' => function ($query) use ($league) {
				$query->where('league_id', $league->id);
			}
		));

		// Link up draft-movie pivot
		$league->players->each(function($player) use($league) {
			$player->movies->map(function($movie) use($league) {
				$lmovie = $league->movies->find($movie->id);
				if($lmovie) {
					$movie->grabLeaguePivot($lmovie);
				}
			});
			$player->buytotal = array_sum($player->movies->map(function($movie) {
				return $movie->league_pivot->price;
			})->toArray());
		});


		$this->layout->title = "Players | ". $league->name;
		$this->layout->content = View::make("league.players", compact("league"));
		$this->layout->javascript = array("league", "players");
		$this->layout->assets = array(
			"js" => array(
				asset("js/vendor/jquery.flot.min.js"),
				asset("js/vendor/jquery.flot.time.min.js"),
				asset("js/vendor/jquery.flot.resize.min.js"),
			)
		);
	}
	public function getChartData(League $league) {
		$query = DB::table("league_user")->select("league_user.user_id", "movie_earnings.date", DB::raw("SUM(movie_earnings.domestic) as earnings"))
		         ->where("league_user.league_id", $league->id)->wherePlayer(1)->whereNotNull("date")
		         ->leftJoin("league_movie_user", function($join) {
		         	$join->on("league_movie_user.user_id", "=", "league_user.user_id");
		         	$join->on("league_movie_user.league_id", "=", "league_user.league_id");
		         })
		         ->leftJoin("movie_earnings", "movie_earnings.movie_id", "=", "league_movie_user.movie_id")
		         ->where('movie_earnings.date', '<=', $league->end_date)
		         ->groupBy("league_user.user_id", "movie_earnings.date")->orderBy("movie_earnings.date", "ASC")
		;

		$earnings = $query->get();
		$data = $league->players->map(function($user) {
			$object = new stdClass;
			$object->label = $user->displayname;
			$object->data = array();
			$object->earliest = Carbon::now()->addHours(6)->hour(0)->timestamp * 1000; // * 1000 is for javascript
			return $object;
		});
		$lookup = array_combine($league->players->modelKeys(), $data->toArray());

		foreach ($earnings as $earned) {
			$timestamp = strtotime($earned->date) * 1000;
			if($lookup[$earned->user_id]->earliest > $timestamp) {
				$lookup[$earned->user_id]->earliest = $timestamp;
			}
			$lookup[$earned->user_id]->data[] = array($timestamp, intval($earned->earnings));
		}
		foreach ($data as $user) {
			$user->data = array_merge(array(array($user->earliest - 60*60*24*1000, 0)), $user->data);
			unset($user->earliest);
		}

		return Response::json($data);
	}

	public function getViewMovies(League $league) {
		// Load extra data
		$league->load(array(
			'movies',
			'movies.latestEarnings',
			'movies.users' => function($query) use($league) {
				$query->where('league_id', $league->id);
			}
		));

		$this->layout->title = "Movies | ". $league->name;
		$this->layout->content = View::make("league.movies", compact("league"));
	}

}