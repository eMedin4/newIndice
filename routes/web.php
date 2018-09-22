<?php

Route::get('/', 'MovieController@tv')->name('tv');
Route::get('/netflix', ['as' => 'netflix', 'uses' => 'MovieController@netflix']);
Route::get('/pelicula/{slug}', ['as' => 'movie', 'uses' => 'MovieController@show']);


/*  */

Route::group([
    'middleware' => ['auth', 'admin'],
    'namespace' => 'IcScraper',
    'prefix' => 'icscraper'
], function() {
    Route::get('/', 'Scraper@show')->name('icscraper.show');
    Route::get('/filmaffinitybyletter', 'MovieScraper@FilmAffinityByLetter')->name('icscraper.filmaffinitybyletter');
});

/* 
    Usuario user user@gmail.com 123456
    Admin admin admin@gmail.com 123456

*/
Auth::routes();

Route::get('logout', 'MovieController@logout')->name('logout');
