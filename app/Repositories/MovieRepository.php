<?php

namespace App\Repositories;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Param;
use App\Models\User;
use App\Models\MovistarTime;

use Carbon\Carbon;

class MovieRepository {

    public function getMovistar()
    {
        /* return Movie::where('check_background', 1)->orderBy('year')->take(20)->get(); */
        /* return MovistarTime::join('movies', 'movistar_times.movie_id', '=', 'movies.id')->where('time', '>', Carbon::now()->subHour())->orderBy('time')->get(); */
        /* return Movie::join('movistar_times', 'movies.id', '=', 'movistar_times.movie_id')->where('time', '>', Carbon::now()->subHour())->orderBy('time')->get(); */
        $records = MovistarTime::where('time', '>', Carbon::now()->subHour())->with('movie')->get();
        return $records->sortByDesc('sort_coeficient');
    }

    public function getMovie($slug)
    {
        return Movie::where('slug', $slug)->first();
    }

}