<?php

class UserSeeder extends Seeder {
	public function run() {
		$this->command->info("Adding users...");

		if(App::environment() != "testing") {
			$admin_user = $this->command->ask("Please enter the username for the admin user: ");
			$admin_email = $this->command->ask("Please enter the email for the admin user: ", Config::get("app.admin_email"));
		} else {
			$admin_user = 'user';
			$admin_email = Config::get("app.admin_email");
		}

		$users = array(
			array(
				'id' => '1',
				'username' => $admin_user,
				'admin' => true,
				'email' => $admin_email
			),
			array(
				'id' => '2',
				'username' => 'shwood',
				'displayname' => 'Brian Brushwood',
			),
			array(
				'id' => '3',
				'username' => 'JustinRYoung',
				'displayname' => 'Justin Robert Young',
			),
			array(
				'id' => '4',
				'username' => 'acedtect',
				'displayname' => 'Tom Merritt',
			),
			array(
				'id' => '5',
				'username' => 'Massawyrm',
				'displayname' => 'C. Robert Cargill',
			),
			array(
				'id' => '6',
				'username' => 'scottjohnson',
				'displayname' => 'Scott Johnson',
			),
			array(
				'id' => '7',
				'username' => 'sarahlane',
				'displayname' => 'Sarah Lane',
			)
		);
		
		foreach ($users as $userdata) {
			User::create($userdata);
		}
	}
}