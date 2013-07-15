<?php

class LeagueMovieSeeder extends Seeder {
	public function run() {
		$this->command->info("Adding movies to the league...");
		$league = League::find(1);
		$movies = Movie::all();
		$wentfor = array(
			1 => 14, 2 => 20, 3 => 46, 4 => 17, 5 => 48, 6 => 15, 7 => 36, 8 => 26, 9 => 10, 10 => 33,
			11 => 17, 12 => 25, 13 => 36, 14 => 28, 15 => 30, 16 => 15, 17 => 11, 18 => 20, 19 => 18, 20 => 14,
			21 => 22, 22 => 9, 23 => 9, 24 => 16, 25 => 10, 26 => 12, 27 => 7, 28 => 7, 29 => 7, 30 => 6
		);
		foreach ($movies as $movie) {
			$league->movies()->attach($movie, array("price" => $wentfor[$movie->id]));
		}

	}
}