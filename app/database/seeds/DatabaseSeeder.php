<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserSeeder');
		$this->call('LeagueSeeder');
		$this->call('LeagueUsersSeeder');
		$this->call('MovieSeeder');
		$this->call('LeagueMovieSeeder');
	}

}