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
        $records = $this->movieRepository->getMovistar();
        $records_1 = $records->splice(0, 1)->first(); //1 elemento (sin colección)
        $records_2 = $records->splice(0, 1)->first(); //1 elemento (sin colección)
        $records_3 = $records->splice(0, 4); //4 elementos
        $records_4 = $records->splice(0, 6); //8 elementos
        $records_5 = $records->splice(0, 4); //4 elementos
    	return view('tv', compact('records_1', 'records_2', 'records_3', 'records_4', 'records_5'));
    }

    public function netflix()
    {

    }

    public function show($slug)
    {
        $record = $this->movieRepository->getMovie($slug);
        return view('movie', compact('record'));
    }

    public function logout()
    {
        Auth::logout();
        return back();
    }
}
