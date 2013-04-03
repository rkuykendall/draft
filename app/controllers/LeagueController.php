<?php

class LeagueController extends BaseController {
	public $layout = 'layout.main';

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

		$this->layout->content = View::make("league.view", array(
			'league' => $league,
		));
	}
}