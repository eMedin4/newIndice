<?php

namespace App\Http\Controllers\IcScraper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Scraper extends Controller
{
    public function show()
    {
        return view('icScraper.index');
    }
}
