<?php
class LeagueSeeder extends Seeder {

	public function run() {
		$this->command->info("Adding leagues...");
		DB::table("leagues")->delete();

		$leagueData = array();
		$leagueData['id'] = 1;
		$leagueData['name'] = 'NSFWshow Summer Movie Draft 2013';
		$leagueData['slug'] = Str::slug('NSFWshow Summer Movie Draft 2013');
		$leagueData['description'] = "It's back, bitches!";
		$leagueData['url'] = 'http://draft.nsfwshow.com/';
		$leagueData['mode'] = 'bid';
		$leagueData['money'] = 100;
		$leagueData['units'] = '₪';
		$leagueData['end_date'] = '2013-09-20';
		$leagueData['featured'] = true;

		League::create($leagueData);
	}
}