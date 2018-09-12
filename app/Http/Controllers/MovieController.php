<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function tv()
    {
    	return view('main');
    }

    public function netflix()
    {

    }

    public function show($slug)
    {

    }
}
