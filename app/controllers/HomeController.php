<?php

class HomeController extends BaseController {

	public $layout = "layout.main";

	public function showWelcome() {
		$leagues = League::all();
		$this->layout->content = View::make("home.index", array(
			"leagues" => $leagues
		));
	}

	public function showLogin() {
		if(!Auth::guest()) {
			Auth::logout();
		}

		$assertion = Input::get("assertion");
		// Check with persona
		$persona = new Guzzle\Http\Client('https://verifier.login.persona.org');
		try {
			$response = $persona->post('verify')->addPostFields(array(
				"assertion" => $assertion,
				"audience" => url()
			))->send();
		} catch(Guzzle\Http\Exception\BadResponseException $e) {
			App::abort(401, 'You are not authorized.');
		}
		$verification = $response->json();
		if(!$verification['status'] == "okay") {
			App::abort(401, 'You are not authorized.');
		}
		$email = $verification["email"];
		// Login on laravel
		if(Auth::attempt(array("email" => $email))) {
			if(Request::ajax()) {
				return Response::json(array("refresh" => true));
			} else {
				return Redirect::back();
			}
		} else {
			// Not an user
			Session::put("register_email", $email);
			if(Request::ajax()) {
				return Response::json(array("redirect" => url("register")));
			} else {
				return Redirect::back();
			}
		}
	}
	public function showLogout() {
		if(Session::has("register_email")) {
			Session::forget("register_email");
		}
		Auth::logout();
		if(Request::ajax()) {
			return Response::json(array("redirect" => url("/")));
		} else {
			return Redirect::to("/");
		}
	}

	public $register_valid_rules = array(
		'username' => array('required', 'unique:users', 'min:2', 'max:15')
	);

	public function getRegister() {
		if(!Auth::guest()) {
			return Redirect::to("/");
		}
		if(!Session::has("register_email")) {
			return Redirect::to("/");
		}
		$email = Session::get("register_email");

		$fakeuser = new User();
		$fakeuser->displayname = "New User";
		$fakeuser->email = $email;
		Auth::setUser($fakeuser);
		$this->layout->content = View::make("register");
		$this->layout->javascript = array("register");
	}

}