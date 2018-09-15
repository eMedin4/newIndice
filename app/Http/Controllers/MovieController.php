<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function logout()
    {
        Auth::logout();
        return back();
    }
}
