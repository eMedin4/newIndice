<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Param;
use App\Models\User;

class TemporalRepository {

    public function temporal()
    {
        $movies = Movie::where('id', '>', 39800)->chunk(500, function ($movies) {
            foreach($movies as $movie) {
    
                if ($movie->fa_rat) {
                    if ($movie->im_rat) {
                        if ($movie->rt_rat) {
                            $value = (($movie->im_rat + $movie->rt_rat / 10 + $movie->fa_rat) / 3);
                        } else {
                            $value = (($movie->im_rat + $movie->fa_rat) / 2);
                        }
                    } else {
                        $value = ($movie->fa_rat);
                    }
                } else {
                    $value = 0;
                }
                $movie->avg = $value;
                $movie->save();
            }
        });
    }

    public function temporal2()
    {
        $movie = Movie::where('title', 'Foxtrot')->get();
        dd($movie);
    }

    public function temporal3()
    {
        $year = 2018;
        $hot = ($year - 2000) / 3;
        if ($hot < 0) $hot = 0;
        $number = (int)exp($hot);
        dd($number);
    }



}