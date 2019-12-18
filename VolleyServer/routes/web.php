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

    Route::get('/api/home/{idU}', 'matchesController@listmatches');

    Route::get('/api/partecipa', 'matchesController@partecipazione');

    Route::get('/api/matchD/{id}/{idG}', 'matchesController@matchDetails');

    Route::get('/api/aggiunta/{idG}/{idP}', 'matchesController@partecipa');

    Route::get('/api/miepartite/{id}', 'matchesController@my_Matches');

    Route::get('/api/terminated/{id}', 'matchesController@partite_terminate');

    Route::get('/api/players/{id}/{idU}', 'ButtonsController@lasciaFeedback');

    Route::post('/api/updateProfile/{id}', 'ProfileController@updateProfile');

    Route::post('/api/deletePart', 'ButtonsController@removePartecipation');

    Route::post('/api/delete', 'ButtonsController@Cancella');






