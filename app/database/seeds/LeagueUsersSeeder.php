<?php

class LeagueUsersSeeder extends Seeder {
	public function run() {
		$league = League::find(1);
		$players = array(2, 3, 4, 5, 6, 7);
		foreach ($players as $playerid) {
			$league->users()->attach($playerid);
		}

	}
}