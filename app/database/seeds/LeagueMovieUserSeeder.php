<?php

class LeagueMovieUserSeeder extends Seeder {

	public $movies = [
		2 => [1, 3, 18, 20, 30],
		3 => [6, 7, 13, 25],
		4 => [10, 14, 17, 24, 28],
		5 => [8, 12, 16, 21, 26],
		6 => [2, 5, 19, 23],
		7 => [4, 9, 11, 15, 22, 27, 29],
	];

	public function run() {
		$league = League::find(1);
		$players = $league->players;

		foreach ($this->movies as $user_id => $movies) {
			foreach ($movies as $movie_id) {
				$players->find($user_id)->movies()->attach($movie_id, array("league_id" => $league->id));
			}
		}

		foreach ($this->movies as $user_id => $movies) {
			Queue::push("UpdateUserEarnings", array(
				"user_id" => $user_id, "league_id" => $league->id, "since" => Carbon::now()->format('U') + 1000
			));
		}
	}
}