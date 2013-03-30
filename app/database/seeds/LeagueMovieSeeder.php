<?php

class LeagueMovieSeeder extends Seeder {
	public function run() {
		$league = League::find(1);
		$movies = Movie::all();
		foreach ($movies as $movie) {
			$league->movies()->attach($movie);
		}

	}
}