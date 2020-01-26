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
Route::get('/variable', 'BaseController@variable')->name('variable');

Route::get('/dictionary', 'DictionaryController@index')->name('dictionary');
Route::get('/dictionary/new', 'DictionaryController@new')->name('new_dictionary');
Route::post('/dictionary/new', 'DictionaryController@save')->name('save_dictionary');
Route::get('/dictionary/{id}', 'DictionaryController@edit')->name('edit_dictionary');
Route::post('/dictionary/{id}', 'DictionaryController@update')->name('update_dictionary');

Route::get('/scenario/new', 'ScenarioController@new')->name('new_scenario');
Route::post('/scenario/new', 'ScenarioController@save')->name('save_scenario');
Route::get('/scenario/{id}', 'ScenarioController@edit')->name('edit_scenario');
Route::post('/scenario/{id}', 'ScenarioController@update')->name('update_scenario');

Route::get('/talktest', 'BaseController@talktest')->name('talktest');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


