<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'BaseController@dashbord')->name('dashbord');
Route::get('/dictionary', 'BaseController@dictionary')->name('dictionary');
Route::get('/variable', 'BaseController@variable')->name('variable');

Route::get('/scenario/new', 'BaseController@new_scenario')->name('new_scenario');
Route::post('/scenario/new', 'BaseController@save_scenario')->name('save_scenario');

Route::get('/scenario/{id}', 'BaseController@scenario')->name('scenario');
Route::post('/scenario/{id}', 'BaseController@update_scenario')->name('update_scenario');

Route::get('/talktest', 'BaseController@talktest')->name('talktest');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
