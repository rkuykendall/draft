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
	/* League views */
	public function getView($leagueID, $leagueSlug = '') {
		if(!($league = League::with('players')->find($leagueID))) {
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
}