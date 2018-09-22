<?php

namespace App\Repositories;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Param;
use App\Models\User;

class MovieRepository {

    public function getMovies()
    {
        return Movie::where('check_background', 1)->orderBy('fa_count')->take(20)->get();
    }



}