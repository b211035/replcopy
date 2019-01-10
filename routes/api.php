<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function() {
	Route::post('/registration', 'ApiController@registration')->name('registration_api');
	Route::post('/dialogue', 'ApiController@dialogue')->name('dialogue_api');
});
