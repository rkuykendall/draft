<?php

class MovieEarningsSeeder extends Seeder {

	public function run() {
		$data = new SplFileObject(app_path("database/seeds/earnings.csv"));
		$data->setFlags(SplFileObject::READ_CSV);

		$movies = Movie::all();

		$rows = [];
		foreach ($data as $row) {
			list($movie_id, $when, $earnings) = $row;
			$when = new Carbon($when, "America/Los_Angeles");

			$rows[] = array(
				"movie_id" => $movie_id, "domestic" => $earnings,
				"date" => $when, "created_at" => $when, "updated_at" => $when
			);
		}
		foreach (array_chunk($rows, 50) as $chunk) { // sqlite limit
			DB::table('movie_earnings')->insert($chunk);
		}

		$latests = DB::table('movie_earnings')->groupBy('movie_id')->lists(DB::raw('max(ID)'), 'movie_id');

		foreach ($latests as $movie_id => $earning_id) {
			$movie = $movies->find($movie_id);
			$movie->latest_earnings_id = $earning_id;
		}
		$movies->map(function($movie) {
			$movie->save();
		});
	}
}