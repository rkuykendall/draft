<?php

class HomeController extends BaseController {

	public $layout = "layout.main";

	public function showWelcome() {
		$leagues = League::all();
		$this->layout->content = View::make("home.index", array(
			"leagues" => $leagues
		));
	}

}