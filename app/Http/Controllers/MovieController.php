<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\MovieRepository;

class MovieController extends Controller
{

    private $movieRepository;

	public function __Construct(movieRepository $movieRepository)
	{
		$this->movieRepository = $movieRepository;
	}
    
    public function tv()
    {
        $movies = $this->movieRepository->getMovies();
        $relatedMovies = $movies->splice(5);
    	return view('tv', compact('movies', 'relatedMovies'));
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
