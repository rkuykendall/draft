<?php
class LeagueSeeder extends Seeder {

	public function run() {
		DB::table("leagues")->delete();

		$leagueData = array();
		$leagueData['id'] = 1;
		$leagueData['name'] = 'NSFWshow Summer Movie Draft 2013';
		$leagueData['slug'] = Str::slug('NSFWshow Summer Movie Draft 2013');
		$leagueData['description'] = <<<EOL
<p>It's back, bitches!</p>
EOL;
		$leagueData['url'] = 'http://draft.nsfwshow.com/';
		$leagueData['mode'] = 'bid';
		$leagueData['money'] = 100;
		$leagueData['units'] = '₪';
		$leagueData['end_date'] = '2013-09-20';

		League::create($leagueData);
	}
}