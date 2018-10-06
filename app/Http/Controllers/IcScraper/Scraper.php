<?php

namespace App\Http\Controllers\IcScraper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Movie;

class Scraper extends Controller
{
    public function show()
    {
        $movies = Movie::paginate(100);;
        return view('icScraper.index', compact('movies'));
        
    }
}
