<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'PagesController@index');
Route::post('/', 'PagesController@showForm');

Route::get('/createTournament', 'CreateTournamentController@getTournament');
Route::post('/createTournament', 'CreateTournamentController@postTournament');

Route::get('/updateTournament/{n}', 'CreateTournamentController@updateTournament');
Route::get('/deleteTournament/{n}', 'CreateTournamentController@deleteTournament');

Route::get('/manageTournament/{n}', 'PagesController@showActualTournament');

Route::get('/listAllTournaments', 'PagesController@showListAllTournament');
Route::get('/createGroup/{n}', 'PagesController@creationGroupsTournament');

Route::get('/listAllTeams/{n}', 'PagesController@showListAllTeams');

Route::get('/updateTeam/{n}', 'CreateTeamController@getTeam');
Route::post('/updateTeam/{n}', 'CreateTeamController@postTeam');
Route::get('/deleteTeam/{n}', 'CreateTeamController@deleteTeam');