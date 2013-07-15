<?php

class LeagueMovieUserSeeder extends Seeder {
	public function run() {
		$this->command->info("Adding movies to users...");
		$league = League::find(1);
		$players = $league->players;

		foreach (array(1, 3, 18, 20, 30) as $movieid) {
			$players->find(2)->movies()->attach($movieid, array("league_id" => $league->id));
		}
		foreach (array(6, 7, 13, 25) as $movieid) {
			$players->find(3)->movies()->attach($movieid, array("league_id" => $league->id));
		}
		foreach (array(10, 14, 17, 24, 28) as $movieid) {
			$players->find(4)->movies()->attach($movieid, array("league_id" => $league->id));
		}
		foreach (array(8, 12, 16, 21, 26) as $movieid) {
			$players->find(5)->movies()->attach($movieid, array("league_id" => $league->id));
		}
		foreach (array(2, 5, 19, 23) as $movieid) {
			$players->find(6)->movies()->attach($movieid, array("league_id" => $league->id));
		}
		foreach (array(4, 9, 11, 15, 22, 27, 29) as $movieid) {
			$players->find(7)->movies()->attach($movieid, array("league_id" => $league->id));
		}
	}
}