<?php
class UserController extends BaseController {
	public $layout = "layout.main";

	public function getView(User $user) {
		$user->load(array("leagues" => function($query) {
			$query->wherePrivate(0);
			$query->wherePlayer(1);
		}));
		$this->layout->content = View::make("users.view", compact("user"));
	}
}