<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Inspired by http://philsturgeon.co.uk/blog/2013/11/build-apis-part-1-useful-database-seeding
		if (App::environment() === 'production') {
			$this->error('NO! BAD USER!');
			return;
		}

		$isMySQL = DB::connection()->getDriverName() == 'mysql';

		Eloquent::unguard();

		if($isMySQL) {
			DB::statement("SET FOREIGN_KEY_CHECKS=0;");
		}

		$tables = array(
			'leagues',
			'league_movie',
			'league_movie_user',
			'league_user',
			'movies',
			'movie_earnings',
			'users',
		);
		foreach ($tables as $table) {
			DB::table($table)->truncate();
		}

		if($isMySQL) {
			DB::statement("SET FOREIGN_KEY_CHECKS=1;");
		}

		$this->call('UserSeeder');
		$this->call('MovieSeeder');
		$this->call('MovieEarningsSeeder');
		$this->call('LeagueSeeder');
		$this->call('LeagueUsersSeeder');
		$this->call('LeagueMovieSeeder');
		$this->call('LeagueMovieUserSeeder');
	}

}