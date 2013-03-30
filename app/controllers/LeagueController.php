<?php

class LeagueController extends BaseController {
	public $layout = 'layout.main';

	public function getView($leagueID, $leagueSlug = '') {
		if(!($league = League::find($leagueID))) {
			App::abort(404);
		}
		if($leagueSlug != $league->slug) {
			return Redirect::to("league/{$league->id}-{$league->slug}");
		}
		$this->layout->content = View::make("league.view", array(
			'league' => $league,
		));
	}
}