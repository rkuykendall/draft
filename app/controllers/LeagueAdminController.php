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
	);

	public function getAdminSettings(League $league) {

		$this->layout->title = "Settings | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.settings", array(
			'league' => $league, "edit_rules" => $this->league_edit_valid_rules
		));

	}

	public function postAdminSettings(League $league) {
		$validator = Validator::make(Input::all(), $this->league_edit_valid_rules);
		if($validator->fails()) {
			Notification::error("Duder something is wrong with your input.");
			return Redirect::action("LeagueAdminController@getAdminSettings", array("league_slug" => $league->slug))->withInput()->withErrors($validator);
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
			return Redirect::action("LeagueAdminController@getAdminSettings", array("league_slug" => $league->slug));
		} else {
			Notification::error("Database error, try again later?");
			return Redirect::action("LeagueAdminController@getAdminSettings", array("league_slug" => $league->slug))->withInput();
		}
	}

	// Users
	public function getAdminUsers(League $league) {

		$this->layout->title = "Users | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.users", array(
			'league' => $league,
		));

	}

	public function postAdminUsers(League $league) {
		// Get the user
		$user = User::whereUsername(Input::get("username"))->first();
		if(!$user) {
			Notification::error("User not found");
			return Redirect::action("LeagueAdminController@getAdminUsers", array("league_slug" => $league->slug))->withInput();
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
		return Redirect::action("LeagueAdminController@getAdminUsers", array("league_slug" => $league->slug));
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
	public function getAdminMovies(League $league) {
		$league->load(
			'movies',
			'movies.users'
		);
		// Preformatted for your satisfaction
		$players = array_merge(array(array("id" => 0, "username" => "- Nobody -")), $league->players->map(function($player) {
			return array("id" => $player->id, "username" => $player->username);
		})->toArray());

		$this->layout->title = "Movies | Admin | ".$league->name;
		$this->layout->content = View::make("league.admin.movies", array(
			"league" => $league, "players" => $players,
		));
	}
	public function postAdminMovies(League $league) {
		$league->load(
			'movies',
			'movies.users'
		);
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

		return Redirect::action("LeagueAdminController@getAdminMovies", array("league_slug" => $league->slug));
	}

}