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
			exit('NO! BAD USER!');
		}

		Eloquent::unguard();

		$tables = array(
			'users', 'movies', 'leagues' // foreign keys handle the rest.
		);
		foreach ($tables as $table) {
			DB::table($table)->truncate();
		}

		$this->call('UserSeeder');
		$this->call('LeagueSeeder');
		$this->call('LeagueUsersSeeder');
		$this->call('MovieSeeder');
		$this->call('LeagueMovieSeeder');
		$this->call('LeagueMovieUserSeeder');
	}

}