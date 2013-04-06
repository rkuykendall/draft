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
Route::get('/', "HomeController@showWelcome");
Route::post('login', array(
	'before' => 'csrf',
	'uses' => "HomeController@showLogin"
));
Route::post('logout', array(
	'before' => 'csrf',
	'uses' => "HomeController@showLogout"
));
Route::get('register', array(
	"before" => 'guest',
	'uses' => "HomeController@getRegister"
));
Route::post('register', array(
	'before' => 'csrf',
	'uses' => "HomeController@postRegister"
));

// League
Route::get('league/list', 'LeagueController@getList');
Route::get('league/create', array(
	'before' => 'auth',
	'uses' => 'LeagueController@getCreate'
));
Route::post('league/create', array(
	'before' => 'auth|csrf',
	'uses' => 'LeagueController@postCreate'
));
Route::get('league/{id}-{slug?}', 'LeagueController@getView');

Route::get('league/{id}-{slug?}/admin/users', 'LeagueController@getAdminUsers');
Route::post('league/{id}-{slug?}/admin/users', 'LeagueController@postAdminUsers');
Route::get('league/{id}-{slug?}/admin/users/lookup/{query}', 'LeagueController@userLookup');
Route::get('league/{id}-{slug?}/admin/movies', 'LeagueController@getAdminMovies');
Route::post('league/{id}-{slug?}/admin/movies', 'LeagueController@postAdminMovies');

