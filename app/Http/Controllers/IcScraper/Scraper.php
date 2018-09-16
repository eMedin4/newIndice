<?php

namespace App\Http\Controllers\IcScraper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Movie;

class Scraper extends Controller
{
    public function show()
    {
        $movies = Movie::all();
        return view('icScraper.index', compact('movies'));
    }
}
