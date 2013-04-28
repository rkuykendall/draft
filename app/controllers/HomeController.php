<?php

class HomeController extends BaseController {
	public $layout = "layout.main";

	public function __construct() {
		$this->beforeFilter("csrf", array("on" => "post"));

		$this->beforeFilter("guest", array("only" => array("getRegister", "postRegister")));

		$this->beforeFilter(function($route, $request) {
			if(!Session::has("register_email")) {
				Notification::error("Session expired, we might get a fresh one by the browser...");
				return Redirect::to("/");
			}
		}, array("only" => array("getRegister", "postRegister")));
	}

	/* ROUTES */

	// Home
	public function getWelcome() {
		$leagues = League::whereFeatured(true)->get();
		$this->layout->content = View::make("home.index", array(
			"leagues" => $leagues
		));
	}

	// Login & Logout
	public function postLogin() {
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
		if($verification['status'] != "okay") {
			App::abort(401, 'You are not authorized.');
		}
		$email = $verification["email"];
		// Login on laravel
		if(Auth::attempt(array("email" => $email, "password" => "moz:persona"), true)) {
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
				return Redirect::to("register");
			}
		}
	}
	public function postLogout() {
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

	// Registration
	public $register_valid_rules = array(
		'username' => 'required|unique:users|min:2|max:15',
		'displayname' => 'max:64'
	);

	public function getRegister() {
		$email = Session::get("register_email");

		$fakeuser = new User();
		$fakeuser->displayname = "New User";
		$fakeuser->email = $email;
		Auth::setUser($fakeuser);
		$this->layout->content = View::make("register", array("register_rules" => $this->register_valid_rules));
		$this->layout->javascript = array("register");
	}
	public function postRegister($value='') {
		$validator = Validator::make(Input::all(), $this->register_valid_rules);
		if ($validator->fails()) {
			Notification::error("Something's wrong, check the fields bellow!");
			return Redirect::to("register")->withInput()->withErrors($validator);
		}
		// Create user
		$user = new User();
		$user->username = Input::get("username");
		$user->displayname = Input::get("displayname");
		$user->email = Session::get("register_email");
		if($user->save()) {
			Auth::login($user, true);
			Notification::success("Welcome {$user->displayname}!");
			return Redirect::to("/");
		} else {
			Notification::error("Save errors :'(");
			return Redirect::to("register")->withInput();
		}
	}
}