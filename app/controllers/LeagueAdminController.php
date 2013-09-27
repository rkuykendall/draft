<?php
class LeagueAdminController extends BaseController {
	public $layout = 'layout.main';

	/* ROUTES */
	// League settings
	public $league_edit_valid_rules = array(
		"name" => "required|max:255",
		"description" => "required",
		"url" => "url",

		"money" => "required|integer",
		"units" => "required|max:16",
		"extra_weeks" => "required|integer|between:1,12",
	);

	public function getSettings(League $league) {
		$max_weeks = $league->maxExtraWeeks();		

		$this->layout->title = "Settings | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.settings", array(
			'league' => $league, "edit_rules" => $this->league_edit_valid_rules, 'max_weeks' => $max_weeks
		));

	}

	public function postSettings(League $league) {
		$validator = Validator::make(Input::all(), $this->league_edit_valid_rules);
		if($validator->fails()) {
			Notification::error("Duder something is wrong with your input.");
			return Redirect::action("LeagueAdminController@getSettings", array("league_slug" => $league->slug))->withInput()->withErrors($validator);
		}

		// Overwrites
		$league->name        = Input::get("name");
		$league->description = Input::get("description");
		$league->url         = Input::get("url");
		$league->private     = Input::get("private") ? true : false;

		$league->money       = Input::get("money");
		$league->units       = Input::get("units");
		$league->extra_weeks = Input::get("extra_weeks");
		if($league->save()) {
			// in case of extra_weeks update
			$league->updateLeagueDates();

			Notification::success("Changes saved!");
			return Redirect::action("LeagueAdminController@getSettings", array("league_slug" => $league->slug));
		} else {
			Notification::error("Database error, try again later?");
			return Redirect::action("LeagueAdminController@getSettings", array("league_slug" => $league->slug))->withInput();
		}
	}

	// Users
	public function getUsers(League $league) {

		$this->layout->title = "Users | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.users", array(
			'league' => $league,
		));

	}

	public function postUsers(League $league) {
		// Get the user
		$user = User::whereUsername(Input::get("username"))->first();
		if(!$user) {
			Notification::error("User not found");
			return Redirect::action("LeagueAdminController@getUsers", array("league_slug" => $league->slug))->withInput();
		}

		try {
			if(Input::get("action") == "add") {
				if(Input::get("type") == "player") {
					$this->addPlayer($league, $user);
					Notification::success("Player added!");
				} elseif(Input::get("type") == "admin") {
					$this->addAdmin($league, $user);
					Notification::success("Admin added!");
				}
			} elseif(Input::get("action") == "remove") {
				if(Input::get("type") == "player") {
					$this->removePlayer($league, $user);
					Notification::success("Player removed!");
				} elseif(Input::get("type") == "admin") {
					$this->removeAdmin($league, $user);
					Notification::success("Admin removed!");
				}
			}
		// In case of an error
		} catch(Exception $e) {
			if($e->getCode() == 0) { // Warning
				Notification::warning($e->getMessage());
			} else { // Error
				Notification::error($e->getMessage());
			}
		}
		return Redirect::action("LeagueAdminController@getUsers", array("league_slug" => $league->slug));
	}
	// Actual handlers
	public function addPlayer(League $league, User $user) {
		$luser = $league->users()->whereUser_id($user->id)->first();

		if(!$luser) {
			// New user to the league, just attach it!
			return $league->users()->attach($user, array("player" => true, "admin" => false));
		} else {
			if ($luser->pivot->player) {
				throw new Exception("User is already a player in this league", 0);
			}
			$luser->pivot->player = true;
			return $luser->pivot->save();
		}
	}

	public function removePlayer(League $league, User $user) {
		$luser = $league->users()->whereUser_id($user->id)->first();

		if(!$luser or !$luser->pivot->player) {
			throw new Exception("This user isn't playing in this league anyway.", 0);
		} else {
			if ($moviecount = $luser->movies()->count()) {
				throw new Exception("Player owns {$moviecount} movies in the league!", 1);
			}
			$luser->pivot->player = false;
			return $this->saveOrRemoveLeagueUser($league, $luser);
		}
	}

	public function addAdmin(League $league, User $user) {
		$luser = $league->users()->whereUser_id($user->id)->first();

		if(!$luser) {
			// New user to the league, just attach it!
			$league->users()->attach($user, array("player" => false, "admin" => true));
			return true;
		} else {
			if ($luser->pivot->admin) {
				throw new Exception("User is already an admin in this league", 0);
			}
			$luser->pivot->admin = true;
			$luser->pivot->save();
			return true;
		}
	}

	public function removeAdmin(League $league, User $user) {
		$luser = $league->users()->whereUser_id($user->id)->first();

		if(!$luser or !$luser->pivot->admin) {
			throw new Exception("This user isn't an admin of this league anyway.", 0);
		} else {
			if ($luser->id == Auth::user()->id) {
				throw new Exception("You can't remove yourself (always must have at least one admin)", 1);
			}
			$luser->pivot->admin = false;
			return $this->saveOrRemoveLeagueUser($league, $luser);
		}
	}

	public function saveOrRemoveLeagueUser(League $league, User $luser) {
		if($luser->pivot->player == false and $luser->pivot->admin == false) {
			// We can forget about the user.
			$league->users()->detach($luser);
		} else {
			$luser->pivot->save();
		}
	}

	// User lookup for autocomplete
	public function userLookup(League $league, $query) {
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
	public function getMovies(League $league) {
		$league->load(array(
			'movies',
			'movies.users' => function($query) use($league) {
				$query->where('league_id', $league->id);
			}
		));
		// Preformatted for your satisfaction
		$players = array_merge(array(array("id" => 0, "username" => "- Nobody -")), $league->players->map(function($player) {
			return array("id" => $player->id, "username" => $player->username);
		})->toArray());

		$this->layout->title = "Movies | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.movies", array(
			"league" => $league, "players" => $players,
		));
	}
	public function postMovies(League $league) {
		$league->load(array(
			'movies',
			'movies.users' => function($query) use($league) {
				$query->where('league_id', $league->id);
			}
		));
		$input = Input::get("movies");

		$errors = array();
		$changes = 0;
		$update_players = array(0);

		if(Input::has('remove') && filter_var(Input::get('remove'), FILTER_VALIDATE_INT) !== false && $movie = $league->movies->find(Input::get('remove'))) {
			// Remove any possible attached players
			foreach ($movie->users as $user) {
				$query = DB::table('league_movie_user')->whereLeagueId($league->id)->whereMovieId($movie->id)->whereUserId($user->id);
				$query->delete();
				if(array_search($user->id, $update_players) === false) {
					$update_players[] = $user->id;
				}
			}
			// Remove movie
			$league->movies()->detach($movie->id);
			// Instead of adding it to changes count, have a seperate notification
			Notification::success('Movie '.e($movie->name).' has been removed from the league.');

		}

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
		// Notifications
		if($changes > 0) {
			Notification::success("{$changes} changes saved!");
		}
		if(count($errors) > 0) {
			Notification::warning("The following errors occured, and were not processed:<ul><li>".implode("</li><li>", $errors)."</li></ul>");
		}

		// Update related players
		foreach ($update_players as $user_id) {
			if($user_id != 0) {
				Queue::push("UpdateUserEarnings", array(
					"user_id" => $user_id, "league_id" => $league->id, "since" => (new DateTime())->format('U')
				));
			}
		}

		return Redirect::action("LeagueAdminController@getMovies", array("league_slug" => $league->slug));
	}

	/**
	 * Adding Movies
	 */
	public function getAddMovies(League $league) {
		$date_range = array(Carbon::now(), $league->maxLastMovieDate());
		$query = Movie::whereBetween('release', $date_range);
		if(count($league->movies)) {
			$query->whereNotIn('id', $league->movies->modelKeys());
		}
		$movies = $query->get();

		$this->layout->title   = "Add Movies | Admin | ".$league->name;
		$this->layout->content = View::make('league.admin.addmovies', compact('league', 'movies', 'date_range'));
	}
	public function postAddMovies(League $league) {
		if(count(Input::get('movies')) == 0) {
			Notification::error('Please choose movies to add.');
			return Redirect::back();
		}

		$date_range = array(Carbon::now(), $league->maxLastMovieDate());

		$moviesQuery = Movie::whereBetween('release', $date_range)->whereIn('id', Input::get('movies'));
		if(count($league->movies)) {
			$moviesQuery->whereNotIn('id', $league->movies->modelKeys());
		}
		$movies = $moviesQuery->get();

		foreach ($movies as $movie) {
			$league->movies()->attach($movie, array('latest_earnings_id' => $movie->latest_earnings_id, 'price' => 0));
		}
		$league->updateLeagueDates();

		Notification::success(count($movies).' movie(s) have been added!');
		return Redirect::action('LeagueAdminController@getAddMovies', array('league_slug' => $league->slug));
	}

	/**
	 * Replace movie
	 */
	public function getMoviesReplace(League $league, Movie $oldmovie) {
		if($league->movies()->where('movie_id', $oldmovie->id)->count() == 0) {
			Notification::error("This movie isn't in the league.");
			return Redirect::action('LeagueAdminController@getMovies', array('league_slug' => $league->slug));
		}
		$date_range = array(Carbon::now(), $league->maxLastMovieDate());
		$movies = Movie::whereBetween('release', $date_range)->whereNotIn('id', $league->movies->modelKeys())->get();
		
		$this->layout->title   = "Replace Movie | Admin | ".$league->name;
		$this->layout->content = View::make('league.admin.replacemovie', compact('league', 'oldmovie', 'movies', 'date_range'));
	}
	public function postMoviesReplace(League $league, Movie $oldmovie) {
		if($league->movies()->where('movie_id', $oldmovie->id)->count() == 0) {
			Notification::error("This movie isn't in the league.");
			return Redirect::action('LeagueAdminController@getMovies', array('league_slug' => $league->slug));
		}
		$date_range = array(Carbon::now(), $league->maxLastMovieDate());

		$new_movie = Movie::whereBetween('release', $date_range)->where('id', Input::get('movie'))->first();
		if(!$new_movie) {
			Notification::error("Couldn't find the replacement movie.");
			return Redirect::action('LeagueAdminController@getMoviesReplace', array('league_slug' => $league->slug, 'movie' => $oldmovie->id));
		}

		DB::table('league_movie')->where('league_id', $league->id)->where('movie_id', $oldmovie->id)->update(array('movie_id' => $new_movie->id));
		DB::table('league_movie_user')->where('league_id', $league->id)->where('movie_id', $oldmovie->id)->update(array('movie_id' => $new_movie->id));
		$league->updateLeagueDates();

		Notification::success(e($oldmovie->name).' has been replaced with '.e($new_movie->name));
		return Redirect::action('LeagueAdminController@getMovies', array('league_slug' => $league->slug));
	}
}