<?php

class UserSeeder extends Seeder {
	public function run() {
		$users = array(
			array(
				'id' => '1',
				'username' => 't2t2',
				'admin' => true,
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