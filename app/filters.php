<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request) {
	// Force back if in regstration mode.
	if(Session::has("register_email") and !($request->is("logout") or $request->is("register"))) {
		if(Auth::check()) { // False positive, aka forgot to delete the session
			Session::forget("register_email");
		}
		return Redirect::to("register");
	}
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if(Auth::guest()) {
		Notification::error("You must be logged in to use this feature!");
		return Redirect::to('/');
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/* Register session check */
Route::filter('register', function() {
	if(!Session::has("register_email")) {
		Notification::error("Session expired, we might get a fresh one by the browser...");
		return Redirect::to("/");
	}
});


/* League filters */
Route::filter('league.admin', function($route) {
	$league = $route->getParameter('league_slug') ?: $route->getParameter('league_id');

	if(!$league->userIsAdmin(Auth::user())) {
		App::abort(404);
	}
});

Route::filter('league.active', function($route) {
	$league = $route->getParameter('league_slug') ?: $route->getParameter('league_id');

	if(!$league->active) {
		Notification::error("League is now archived.");
		return Redirect::action("LeagueController@getView", array('league_slug' => $league->slug));
	}
});


