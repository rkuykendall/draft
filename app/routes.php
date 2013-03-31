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

// Home
Route::get('/', "HomeController@showWelcome");
Route::post('login', array('before' => 'csrf',
	'uses' => "HomeController@showLogin"));
Route::post('logout', array('before' => 'csrf',
	'uses' => "HomeController@showLogout"));
Route::get('register', "HomeController@getRegister");
Route::post('register', array('before' => 'csrf',
	'uses' => "HomeController@postRegister"));


Route::get('league/{id}-{slug?}', 'LeagueController@getView');
Route::controller('league', 'LeagueController');