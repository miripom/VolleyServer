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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/match_types', 'matchesController@match_type');

Route::get('/api/role_types', 'Auth\AuthController@role_type');

Route::post('/api/newMatch', 'matchesController@addMatch');

Route::get('/api/matches', 'matchesController@listmatches');

Route::get('/api/partecipationOrg', 'matchesController@partecipazioneOrg');

Route::get('/api/matchD/{id}', 'matchesController@matchDetails');

Route::get('/api/partecipation/{id}', 'matchesController@partecipa');

Route::get('/api/mymatches', 'matchesController@my_Matches');

Route::get('/api/terminated', 'matchesController@partite_terminate');

// rotta dei feedback cambiare nome   Route::get('/api/players/{id}/{idU}', 'ButtonsController@lasciaFeedback');

Route::post('/api/updateDescription', 'ProfileController@updateDescr');

Route::get('/api/players/{id}', 'matchesController@allplayers');

Route::get('/api/checkPartecipation/{id}', 'matchesController@checkP');

Route::delete('/api/deletePartecipation/{id}', 'ButtonsController@removePartecipation');

Route::delete('/api/deletematch/{id}', 'ButtonsController@Cancella');

Route::get('api/feedbackP/{id}', 'matchesController@feedbackPlayers');

Route::post('api/votazione', 'feedbackController@addVoto');

Route::post('api/checkFeedback', 'feedbackController@checkFeedback');

Route::get('api/commenti', 'feedbackController@getCommenti');






