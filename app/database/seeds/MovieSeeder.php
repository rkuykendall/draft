<?php

class MovieSeeder extends Seeder {
	public function run() {
		$movies = array(
			array("name" => "Oblivion", "boxmojo_id" => "oblivion", "release" => new DateTime("4/19/2013")),
			array("name" => "Iron Man 3", "boxmojo_id" => "ironman3", "release" => new DateTime("5/3/2013")),
			array("name" => "The Great Gatsby", "boxmojo_id" => "greatgatsby2012", "release" => new DateTime("				5/10/2013")),
			array("name" => "Star Trek Into Darkness", "boxmojo_id" => "startrek12", "release" => new DateTime("				5/17/2013")),
			array("name" => "Epic", "boxmojo_id" => "leafmen", "release" => new DateTime("5/24/2013")),
			array("name" => "Fast & Furious 6", "boxmojo_id" => "fast6", "release" => new DateTime("5/24/2013")),
			array("name" => "The Hangover Part III", "boxmojo_id" => "hangover3", "release" => new DateTime("				5/24/2013")),
			array("name" => "Now You See Me", "boxmojo_id" => "nowyouseeme", "release" => new DateTime("5/31/2013")				),
			array("name" => "After Earth", "boxmojo_id" => "1000ae", "release" => new DateTime("6/7/2013")),
			array("name" => "The Internship", "boxmojo_id" => "internship", "release" => new DateTime("6/7/2013")),
			array("name" => "Man of Steel", "boxmojo_id" => "superman2012", "release" => new DateTime("6/14/2013"))				,
			array("name" => "This is the End", "boxmojo_id" => "rogenhilluntitled", "release" => new DateTime("				6/14/2013")),
			array("name" => "Monsters University", "boxmojo_id" => "monstersinc2", "release" => new DateTime("				6/21/2013")),
			array("name" => "World War Z", "boxmojo_id" => "worldwarz", "release" => new DateTime("6/21/2013")),
			array("name" => "White House Down", "boxmojo_id" => "whitehousedown", "release" => new DateTime("				6/28/2013")),
			array("name" => "The Heat", "boxmojo_id" => "bullockmccarthy", "release" => new DateTime("6/28/2013")),
			array("name" => "Despicable Me 2", "boxmojo_id" => "despicableme2", "release" => new DateTime("7/3/2013				")),
			array("name" => "Lone Ranger", "boxmojo_id" => "loneranger", "release" => new DateTime("7/3/2013")),
			array("name" => "Grown Ups 2", "boxmojo_id" => "grownups2", "release" => new DateTime("7/12/2013")),
			array("name" => "Pacific Rim", "boxmojo_id" => "pacificrim", "release" => new DateTime("7/12/2013")),
			array("name" => "Turbo", "boxmojo_id" => "turbo", "release" => new DateTime("7/17/2013")),
			array("name" => "Red 2", "boxmojo_id" => "red2", "release" => new DateTime("7/19/2013")),
			array("name" => "The Wolverine", "boxmojo_id" => "wolverine2", "release" => new DateTime("7/26/2013")),
			array("name" => "The Smurfs 2", "boxmojo_id" => "smurfs2", "release" => new DateTime("7/31/2013")),
			array("name" => "300: Rise of An Empire", "boxmojo_id" => "300sequel", "release" => new DateTime("				8/2/2013")),
			array("name" => "Elysium", "boxmojo_id" => "elysium", "release" => new DateTime("8/9/2013")),
			array("name" => "Planes", "boxmojo_id" => "planes", "release" => new DateTime("8/9/2013")),
			array("name" => "Kick-Ass 2", "boxmojo_id" => "kickass2", "release" => new DateTime("8/16/2013")),
			array("name" => "The World's End", "boxmojo_id" => "worldsend", "release" => new DateTime("8/23/2013"))
		);

		foreach ($movies as $moviedata) {
			Movie::create($moviedata);
		}
	}
}