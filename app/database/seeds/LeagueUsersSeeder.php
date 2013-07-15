<?php

class LeagueUsersSeeder extends Seeder {
	public function run() {
		$this->command->info("Adding users to the league...");
		$league = League::find(1);
		$players = array(2, 3, 4, 5, 6, 7);
		foreach ($players as $playerid) {
			$league->users()->attach($playerid);
		}

	}
}