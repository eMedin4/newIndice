<?php


Route::get('/', function () {
    return view('welcome');
});


Route::get('/tv', ['as' => 'tv', 'uses' => 'MovieController@tv']);
Route::get('/netflix', ['as' => 'netflix', 'uses' => 'MovieController@netflix']);
Route::get('/pelicula/{slug}', ['as' => 'movie', 'uses' => 'MovieController@show']);
