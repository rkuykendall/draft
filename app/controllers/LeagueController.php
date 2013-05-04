<?php

class LeagueController extends BaseController {
	public $layout = 'layout.main';

	public function __construct() {
		$this->beforeFilter("auth", array("only" => array(
			"getCreate", "postCreate",
			"getAdminSettings", "postAdminSettings", "getAdminUsers", "postAdminUsers", "userLookup", "getAdminMovies", "postAdminMovies"
		)));

		$this->beforeFilter("csrf", array("on" => "post"));
	}

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
			$league->movies()->sync(Config::get("draft.league_defaults.movies"));
			Notification::success("GOOD LUCK WARIROR");
			return Redirect::action("LeagueController@getView", array($league->id, $league->slug));
		} else {
			Notification::error("Whoops, the database took a dump. Try again later maybe?");
			return Redirect::action("LeagueController@getCreate")->withInput();
		}
	}

	/* League views */
	public function getView($leagueID, $leagueSlug = '') {
		if(!($league = League::with('users')->find($leagueID))) {
			App::abort(404);
		}
		if($leagueSlug != $league->slug) {
			return Redirect::to("league/{$league->id}-{$league->slug}");
		}
		$league->load('movies');
		$league->movies->load('latestEarnings');
		$league->players->load('movies');
		// Link up draft-movie pivot
		$league->movies->each(function($movie) {
			if(!$movie->latestEarnings) {
				$movie->setRelation("latestEarnings", new MovieEarning(array("domestic" => 0)));
			}
		});
		$league->players->each(function($player) use($league) {
			$player->movies->map(function($movie) use($league) {
				$lmovie = $league->movies->find($movie->id);
				if($lmovie) {
					$movie->grabLeaguePivot($lmovie);
				}
			});
			$player->buytotal = array_sum($player->movies->map(function($movie) {
				return $movie->lpivot->price;
			})->toArray());
		});

		$this->layout->title = $league->name;
		$this->layout->content = View::make("league.view", array(
			'league' => $league,
		));
	}
	public function getViewPlayers($leagueID, $leagueSlug = '') {
		$league = League::findOrFail($leagueID);
		if($leagueSlug != $league->slug) {
			return Redirect::to("league/{$league->id}-{$league->slug}/players");
		}

		$league->load('users');
		$league->load('movies');
		$league->movies->load('latestEarnings');
		$league->players->load('movies');
		// Link up draft-movie pivot
		$league->movies->each(function($movie) {
			if(!$movie->latestEarnings) {
				$movie->setRelation("latestEarnings", new MovieEarning(array("domestic" => 0)));
			}
		});
		$league->players->each(function($player) use($league) {
			$player->movies->map(function($movie) use($league) {
				$lmovie = $league->movies->find($movie->id);
				if($lmovie) {
					$movie->grabLeaguePivot($lmovie);
				}
			});
			$player->buytotal = array_sum($player->movies->map(function($movie) {
				return $movie->lpivot->price;
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
	public function getChartData($leagueID) {
		$league = League::findOrFail($leagueID);

		$query = DB::table("league_user")->select("league_user.user_id", "movie_earnings.date", DB::raw("SUM(movie_earnings.domestic) as earnings"))
		         ->where("league_user.league_id", $league->id)->wherePlayer(1)->whereNotNull("date")
		         ->leftJoin("league_movie_user", function($join) {
		         	$join->on("league_movie_user.user_id", "=", "league_user.user_id");
		         	$join->on("league_movie_user.league_id", "=", "league_user.league_id");
		         })
		         ->leftJoin("movie_earnings", "movie_earnings.movie_id", "=", "league_movie_user.movie_id")
		         ->groupBy("league_user.user_id", "movie_earnings.date")->orderBy("movie_earnings.date", "ASC")
		;

		$earnings = $query->get();
		$data = $league->players->map(function($user) {
			$object = new stdClass;
			$object->label = $user->displayname;
			$object->data = array();
			$object->earliest = Carbon::tomorrow()->timestamp * 1000; // * 1000 is for javascript
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

		d($data);
		return "";
	}

	/* Admin functions */
	// League settings
	public $league_edit_valid_rules = array(
		"name" => "required|max:255",
		"description" => "required",
		"url" => "url",

		"money" => "required|integer",
		"units" => "required|max:16",
	);
	public function getAdminSettings($leagueID, $leagueSlug = '') {
		if(!($league = League::with('users')->find($leagueID))) {
			App::abort(404);
		}
		if(!$league->userIsAdmin(Auth::user())) {
			App::abort(404);
		}

		$this->layout->title = "Settings | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.settings", array(
			'league' => $league, "edit_rules" => $this->league_edit_valid_rules
		));
	}
	public function postAdminSettings($leagueID, $leagueSlug = '') {
		if(!($league = League::with('users')->find($leagueID))) {
			App::abort(404);
		}
		if(!$league->userIsAdmin(Auth::user())) {
			App::abort(404);
		}

		$validator = Validator::make(Input::all(), $this->league_edit_valid_rules);
		if($validator->fails()) {
			Notification::error("Duder something is wrong with your input.");
			return Redirect::action("LeagueController@getAdminSettings", array($league->id, $league->slug))->withInput()->withErrors($validator);
		}

		// Overwrites
		$league->name        = Input::get("name");
		$league->description = Input::get("description");
		$league->url         = Input::get("url");
		$league->private     = Input::get("private") ? true : false;

		$league->money       = Input::get("money");
		$league->units       = Input::get("units");
		if($league->save()) {
			Notification::success("Changes saved!");
			return Redirect::action("LeagueController@getAdminSettings", array($league->id, $league->slug));
		} else {
			Notification::error("Database error, try again later?");
			return Redirect::action("LeagueController@getAdminSettings", array($league->id, $league->slug))->withInput();
		}
	}
	// Users
	public function getAdminUsers($leagueID, $leagueSlug = '') {
		if(!($league = League::with('users')->find($leagueID))) {
			App::abort(404);
		}
		if(!$league->userIsAdmin(Auth::user())) {
			App::abort(404);
		}

		$this->layout->title = "Users | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.users", array(
			'league' => $league,
		));
	}
	public function postAdminUsers($leagueID, $leagueSlug = '') {
		if(!($league = League::find($leagueID))) {
			App::abort(404);
		}
		if(!$league->userIsAdmin(Auth::user())) {
			App::abort(404);
		}

		// Get the user
		$user = User::whereUsername(Input::get("username"))->first();
		if(!$user) {
			Notification::error("User not found");
			return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug))->withInput();
		}
		// Check if user is already connected to the league
		$luser = $league->users()->whereUser_id($user->id)->first();

		if(!$luser && Input::get("action") == "add") {
			// New user to the league, just attach it!
			$league->users()->attach($user, array("player" => Input::get("type") == "player" ? true : false, "admin" => Input::get("type") == "admin" ? true : false));
			Notification::success("User added!");
			return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
		} elseif(!$luser && Input::get("action") == "remove") {
			// New user to the league, can't be removed. Probably a race condition.
			Notification::warning("I don't think he's part of the league...");
			return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
		} elseif($luser && Input::get("action") == "add") {
			if(Input::get("type") == "player") {
				// I mean might as well check...
				if ($luser->pivot->player) {
					Notification::warning("User is already a player");
					return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
				}
				$luser->pivot->player = true;
				$luser->pivot->save();
				Notification::success("User added!");
				return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
			}
			if(Input::get("type") == "admin") {
				if ($luser->pivot->admin) {
					Notification::warning("User is already an admin");
					return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
				}
				$luser->pivot->admin = true;
				$luser->pivot->save();
				Notification::success("User added!");
				return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
			}
		} elseif($luser && Input::get("action") == "remove") {
			if(Input::get("type") == "player") {
				if ($moviecount = $luser->movies()->count()) {
					Notification::error("User owns {$moviecount} movies in the league!");
					return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
				}
				if (!$luser->pivot->player) {
					Notification::warning("User is already not a player");
					return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
				}
				$luser->pivot->player = false;
				$message = "Player removed";
			} elseif (Input::get("type") == "admin") {
				if ($luser->id == Auth::user()->id) {
					Notification::error("You can't remove yourself (always must have at least one admin)");
					return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
				}
				if (!$luser->pivot->admin) {
					Notification::warning("User is already not an admin");
					return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
				}
				$luser->pivot->admin = false;
				$message = "Admin removed";
			}
			if($luser->pivot->player == false and $luser->pivot->admin == false) {
				// We can forget about the user.
				$league->users()->detach($luser);
			} else {
				$luser->pivot->save();
			}
			Notification::success($message);
			return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug));
		}
		// Fallback
		return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug))->withInput();
	}

	public function userLookup($leagueID, $leagueSlug = '', $query) {
		if(!($league = League::find($leagueID))) {
			App::abort(404);
		}
		if(!$league->userIsAdmin(Auth::user())) {
			App::abort(404);
		}

		$ids = array();
		if(Input::get('type') == 'admin') {
			$lusers = $league->admins()->get(array('user_id'));
		} else {
			$lusers = $league->players()->get(array('user_id'));
		}
		foreach ($lusers as $key => $value) { $ids[] = $value->user_id; }

		$userQuery = User::where('username', 'LIKE', '%'.htmlentities($query).'%');
		count($ids) > 0 ? $userQuery->whereNotIn('id', $ids) : null;
		$users = $userQuery->take(6)->get(array('username'));

		return Response::json(array('success' => true, 'data' => $users->toArray()));
	}

	// Movies
	public function getAdminMovies($leagueID, $leagueSlug = '') {
		if(!($league = League::with("users", "movies")->find($leagueID))) {
			App::abort(404);
		}
		if(!$league->userIsAdmin(Auth::user())) {
			App::abort(404);
		}

		$league->movies->load("users");
		// Preformatted for your satisfaction
		$players = array_merge(array(array("id" => 0, "username" => "- Nobody -")), $league->players->map(function($player) {
			return array("id" => $player->id, "username" => $player->username);
		})->toArray());

		$this->layout->title = "Movies | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.movies", array(
			"league" => $league, "players" => $players,
		));
	}
	public function postAdminMovies($leagueID, $leagueSlug = '') {
		if(!($league = League::with("users", "movies")->find($leagueID))) {
			App::abort(404);
		}
		if(!$league->userIsAdmin(Auth::user())) {
			App::abort(404);
		}

		$league->movies->load("users");
		$input = Input::get("movies");

		$errors = array();
		$changes = 0;
		$update_players = array(0);

		foreach ($league->movies as $movie) {
			// Bought for = $movie->pivot->price
			$_price = $input[$movie->id]["price"] ?: 0;
			if(filter_var($_price, FILTER_VALIDATE_INT) !== false) {
				if($movie->pivot->price != $_price) {
					$movie->pivot->price = $_price;
					$movie->pivot->save();
					$changes++;
				}
			} else {
				$errors[] = 'Movie '.e($movie->name).' was bought for an invalid price.';
			}
			// User = $movie->users[0]
			$_player = $input[$movie->id]["player"] ?: 0;
			if($_player == 0 or $league->players->find($_player)) {
				if(isset($movie->users[0])) {
					$currowner = $movie->users[0]->id;
				} else {
					$currowner = 0;
				}
				if($_player != $currowner) {
					if($currowner == 0) {
						$movie->users()->attach($_player, array("league_id" => $league->id));
					} else {
						$query = DB::table('league_movie_user')->whereLeagueId($league->id)->whereMovieId($movie->id);
						if($_player) {
							$query->update(array("user_id" => $_player));
						} else {
							$query->delete();
						}
					}
					$changes++;
					if(array_search($_player, $update_players) === false) {
						$update_players[] = $_player;
					}
					if(array_search($currowner, $update_players) === false) {
						$update_players[] = $currowner;
					}
				}
			} else {
				$errors[] = 'Movie '.e($movie->name).' was bought by an UFO.';
			}
		}
		Notification::success("{$changes} changes saved!");
		foreach ($update_players as $user_id) {
			if($user_id != 0) {
				Queue::push("UpdateUserEarnings", array(
					"user_id" => $user_id, "league_id" => $league->id, "since" => (new DateTime())->format('U')
				));
			}
		}
		if(count($errors) > 0) {
			Notification::warning("The following errors occured, and were not processed:<ul><li>".implode("</li><li>", $errors)."</li></ul>");
		}

		return Redirect::action("LeagueController@getAdminMovies", array($league->id, $league->slug));
	}
}