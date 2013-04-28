<?php
class UserController extends BaseController {
	public $layout = "layout.main";

	public function getView($username) {
		$user = User::with(array("leagues" => function($query) {
			$query->wherePrivate(0);
			$query->wherePlayer(1);
		}))->whereUsername($username)->first();
		if(!$user) {
			App::abort(404);
		}
		$this->layout->content = View::make("users.view", compact("user"));
	}
}