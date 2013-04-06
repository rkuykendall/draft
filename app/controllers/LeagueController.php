<?php

class LeagueController extends BaseController {
	public $layout = 'layout.main';

	public function getList() {
		$leagues = League::all();
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

		$league->mode        = Config::get("draft.league_defaults.mode");
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
		$league->players->load('movies');
		// Link up draft-movie pivot
		$league->players->each(function($player) use($league) {
			$player->movies->map(function($movie) use($league) {
				$lmovie = $league->movies->find($movie->id);
				if($lmovie) {
					$movie->grabLeaguePivot($lmovie);
				}
			});
			$player->buytotal = array_sum($player->movies->map(function($movie) {
				return $movie->lpivot->price;
			}));
		});

		$this->layout->title = $league->name;
		$this->layout->content = View::make("league.view", array(
			'league' => $league,
		));
	}

	/* Admin functions */
	public function getAdminUsers($leagueID, $leagueSlug = '') {
		if(!($league = League::with('users')->find($leagueID))) {
			App::abort(404);
		}
		if(!$league->admins->contains(Auth::user()->id)) {
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
		if(!$league->admins->contains(Auth::user()->id)) {
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
		}
		// TODO: Remove

		return Redirect::action("LeagueController@getAdminUsers", array($league->id, $league->slug))->withInput();
	}
}