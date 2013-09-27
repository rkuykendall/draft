<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*
 * HEY! Before editing this!
 *
 * If you were planning to add a route that Brian missaid on air, DON'T.
 * This file would grow to 3 MB.
 */

// Home
Route::get('/', "HomeController@getWelcome");
Route::post('login', array('before' => 'csrf', 'uses' => "HomeController@postLogin"));
Route::post('logout', array('before' => 'csrf', 'uses' => "HomeController@postLogout"));
Route::get('register', array('before' => 'guest|register', 'uses' => "HomeController@getRegister"));
Route::post('register', array('before' => 'csrf|guest|register', 'uses' => "HomeController@postRegister"));

// League
Route::get('league/list', 'LeagueController@getList');
Route::get('league/create', array('before' => 'auth', 'uses' => 'LeagueController@getCreate'));
Route::post('league/create', array('before' => 'csrf|auth', 'uses' => 'LeagueController@postCreate'));

// One League
Route::get('league/{league_slug}', 'LeagueController@getView');
Route::get('league/{league_slug}/players', 'LeagueController@getViewPlayers');
Route::get('league/{league_slug}/movies', 'LeagueController@getViewMovies');

Route::get('league/{league_id}/chart', 'LeagueController@getChartData');

// League Admin
Route::group(array('before' => 'auth|league.admin|league.active'), function() {

	Route::get('league/{league_slug}/admin/settings', 'LeagueAdminController@getSettings');
	Route::post('league/{league_slug}/admin/settings', array('before' => 'csrf', 'uses' => 'LeagueAdminController@postSettings'));

	Route::get('league/{league_slug}/admin/users', 'LeagueAdminController@getUsers');
	Route::post('league/{league_slug}/admin/users', array('before' => 'csrf', 'uses' => 'LeagueAdminController@postUsers'));

	Route::get('league/{league_id}/admin/users/lookup/{query?}', 'LeagueAdminController@userLookup');

	Route::get('league/{league_slug}/admin/movies', 'LeagueAdminController@getMovies');
	Route::post('league/{league_slug}/admin/movies', array('before' => 'csrf', 'uses' => 'LeagueAdminController@postMovies'));
	
	Route::get('league/{league_slug}/admin/movies/add', 'LeagueAdminController@getAddMovies');
	Route::post('league/{league_slug}/admin/movies/add', array('before' => 'csrf', 'uses' => 'LeagueAdminController@postAddMovies'));
	
	Route::get('league/{league_slug}/admin/movies/replace/{movie}', 'LeagueAdminController@getMoviesReplace');
	Route::post('league/{league_slug}/admin/movies/replace/{movie}', array('before' => 'csrf', 'uses' => 'LeagueAdminController@postMoviesReplace'));

});
// User
Route::get("user/{username}", "UserController@getView");
