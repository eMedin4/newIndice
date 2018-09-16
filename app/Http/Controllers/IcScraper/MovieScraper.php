<?php

namespace App\Http\Controllers\IcScraper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Goutte\Client;
use Carbon\Carbon;

use App\Repositories\scraperRepository;

class MovieScraper extends Controller
{

    private $scrapRepository;

    public function __Construct(ScraperRepository $scraperRepository)
	{
		$this->scraperRepository = $scraperRepository;
        set_time_limit(14400);
        $this->updateAllGenres();
	}

    public function FilmAffinityByLetter()
    {
        $url = "https://www.filmaffinity.com/es/allfilms_X_1.html";
        $client = new Client();
        $crawler = $client->request('GET', $url);

        if ($client->getResponse()->getStatus() !== 200) {
			return view('icScraper.error', ['message' => 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus()]);
        } 
        
        ob_start(); //iniciamos el output buffering https://stackoverflow.com/questions/5415665/show-results-while-script-is-still-executing

        for ($i=1; $i<=5; $i++) {

			echo 'scrapeamos pagina: ' . $crawler->getUri() . '<br>';

			//SCRAPEAMOS PÁGINA
			$crawler->filter('.movie-card')->each(function($element) use($client) {

				//SCRAPEAMOS MOVIE CARD
				$card = $this->cardScrap($element);

				//SI YA EXISTE SALIMOS
				if (!$this->scrapRepository->checkUpdated($card['id'], $days=60)) return;

				//SI TIENE 10 O MENOS VOTOS SALIMOS
				if ($card['countScore'] < 10) return;

				//SI ES UNA SERIE O UN CORTO SALIMOS
				if (preg_match('(\(Serie de TV\)|\(C\))', $card['title'])) return;

				//CLICK Y ENTRAMOS
				$crawler = $client->click($card['href']->link());

				echo 'entramos en ' . $crawler->getUri() . '<br>';
					
				//SCRAPEAMOS PELICULA
				$data = $this->getMovie($crawler);
				
				//ERROR AL SCRAPEAR LA PELICULA
				if (array_key_exists('error', $data)) {
					echo 'error' . $data['error'] . '<br>';

				//PELÍCULAS RECHAZADAS
				} elseif (array_key_exists('reject', $data)) {
					echo 'error 2' . $data['reject'] . '<br>';
					//hacer algo con las rechazadas?

				//OK AL GUARDAR LA PELICULA
				} else {
					$movie = $this->scraperRepository->build($data);
				}

			});

			//AVANZAMOS PÁGINA
			if ($crawler->filter('.pager .current')->nextAll()->count()) {
                $upPage = $crawler->filter('.pager .current')->nextAll()->link();
                $crawler = $client->click($upPage);             
            //SI YA NO HAY MAS SALIMOS DEL BUCLE FOR
            } else {
            	break;
            }
		}
        
    }

    public function cardScrap($element)
	{
		$result['href']  = $element->filter('.movie-card h3 a'); 
		$result['id'] = $this->format->faId($result['href']->attr('href'));
		$result['title'] = $element->filter('.movie-card h3 a')->text(); 
		$result['score'] = $this->format->score($element->filter('.avg-rating')->text());
		$result['countScore'] = $this->format->integer($this->format->getElementIfExist($element, '.rat-count', 0));
		return $result;
    }
    




    /*
    |--------------------------------------------------------------------------
    |
    |   SCRAPEO DE UNA PELÍCULA
    |
    |--------------------------------------------------------------------------
    */

    public function getMovie($crawler) 
	{

		/*
	    |--------------------------------------------------------------------------
	    | EN FILMAFFINITY
	    |--------------------------------------------------------------------------
	    */

		/* fa_id */
		if ($crawler->filter('.ntabs a')->count())
			$data['fa_id'] = $this->format->faId($crawler->filter('.ntabs a')->eq(0)->attr('href'));
		else return ['error' => 'No se encuentra ID de filmaffinity en la clase .ntabs a'];
		
		/* title */
		$data['title'] = $crawler->filter('#main-title span')->text();
		$data['title'] = $this->format->removeString($data['title'], '(TV)');

		/* fa_rat y fa_count */
		if ($this->format->getElementIfExist($crawler, '#movie-rat-avg', NULL) && $this->format->getElementIfExist($crawler, '#movie-count-rat span', NULL)) {
			$data['fa_rat'] = $this->format->float($crawler->filter('#movie-rat-avg')->text());
			$data['fa_count'] = $this->format->integer($crawler->filter('#movie-count-rat span')->text());
		} else {
			$data['fa_rat'] = $data['fa_count'] = NULL;
		}

		//Construimos array con los datos de la table(no tienen ids)
        $table = $crawler->filter('.movie-info dt')->each(function($element) {
            return [$element->text() => $element->nextAll()->text()];
        });
        //Devuelve un array de arrays, lo convertimos a array normal
        foreach ($table as $key => $value) { 
            $table2[key($value)] = current($value);
        }

        /* DATOS DE LA TABLA DE FA */
		$data['year'] = $this->format->getValueIfExist($table2, 'Año');
		$data['original_title'] = $this->format->cleanData($this->format->getValueIfExist($table2, 'Título original'));
		$data['country'] = $this->format->cleanData($this->format->getValueIfExist($table2, 'País'));
		$data['duration'] = $this->format->integer($this->format->getValueIfExist($table2, 'Duración'));
		$data['fa_review'] = $this->format->removeString($this->format->getValueIfExist($table2, 'Sinopsis'), '(FILMAFFINITY)');

		if ($data['duration'] < 30) return ['reject' => $data['fa_id'] . ': Dura menos de 30 minutos!'];

		/*
	    |--------------------------------------------------------------------------
	    | THEMOVIEDB
	    |--------------------------------------------------------------------------
	    */

	    /* tm_id */
	    $data['tm_id'] = $this->format->searchTmdbId($data['fa_id'], $data['title'], $data['original_title'], $data['year']);
	    if (is_null($data['tm_id'])) {
	    	if ($data['fa_count'] > 300) {
	    		return ['error' => $data['fa_id'] . ' : No se encuentra en Themoviedb'];
	    	} else {
	    		return ['reject' => $data['fa_id'] . ' : No se encuentra en Themoviedb pero tienen muy pocos votos, las dejamos fuera'];
	    	}
	    } 

	    /* LLAMADA AL API DE TMDB PARA EL RESTO DE DATOS */
		$tmdbapi = file_get_contents('https://api.themoviedb.org/3/movie/' . $data['tm_id'] . '?api_key=' . env('TMDB_API_KEY') . '&language=es&append_to_response=credits');
		$tmdb = json_decode($tmdbapi, true);

		$data['credits'] = $tmdb['credits'];
		$data['genres'] = $tmdb['genres'];	
		$data['im_id'] = $this->format->getValueIfExist($tmdb, 'imdb_id');
		$data['tm_review'] = $this->format->getValueIfExist($tmdb, 'overview');
		$data['poster'] = $this->format->getValueIfExist($tmdb, 'poster_path');
		$data['background'] = $this->format->getValueIfExist($tmdb, 'backdrop_path');

		/*
	    |--------------------------------------------------------------------------
	    | IMDB
	    |--------------------------------------------------------------------------
	    */

	    /* SI NO EXISTE ID DE IMDB TERMINAMOS*/
	    if (is_null($data['im_id'])) return $data;


		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => 'http://www.omdbapi.com/?i=' . urlencode($data['imdb_id']) . '&plot=full&apikey=' . env('TMDB_API_KEY'),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 10,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "{}",
		]);

		$response = curl_exec($curl);
		$error = curl_error($curl);

		if ($error) {
			$response = curl_exec($curl);
			$error = curl_error($curl);
			if ($error) {
				return ['error' => $data['Fa Id'] . ' : Error al scrapear de omdb'];
			}
		}

		curl_close($curl);
		$imdb = json_decode($response, true);

        /* SI LA RESPUESTA DEL OMDB API DA FALSE TAMBIEN TERMINAMOS */
        if ($imdb['Response'] == false) return $data;

        if (isset($imdb['imdbRating']) && $imdb['imdbRating'] != 'N/A')
        	$data['im_rat'] = $this->format->float($imdb['imdbRating']);

        if (isset($imdb['imdbVotes']) AND $imdb['imdbVotes'] != 'N/A')
        	$data['im_count'] = $this->format->integer($imdb['imdbVotes']);

        foreach ($imdb['Ratings'] as $ratings) {
        	if ($ratings['Source'] == 'Rotten Tomatoes') {
        		$data['rt_rat'] = $this->format->integer($ratings['Value']);
        	}
        }

        if (isset($imdb['tomatoURL']) AND $imdb['tomatoURL'] != 'N/A')
        	$data['rt_url'] = $imdb['tomatoURL'];

		return $data;
    }





    /*
    |--------------------------------------------------------------------------
    |
    |   FORMATS
    |
    |--------------------------------------------------------------------------
    */

    // EXTRAE EL ID DE /es/film422703.html
	public function faId($value)
	{
		$value = substr($value, 8); //elimina 8 primeros carácteres
		$value = substr($value, 0, -5); //elimina 5 últimos carácteres
		$value = $this->Integer($value);
		return $value;
    }
    
    //ELIMINA STRING DE UNA CADENA DE TEXTO VALUE
	public function removeString($value, $string)
	{
		$string = preg_quote($string); //escapa los carácteres necesarios
		return trim(preg_replace('/' . $string . '/', '', $value));
    }
    
    // DEVUELVE EL TEXTO SI EXISTE LA CLASE CSS O EL DEFAULT SI NO
	public function getElementIfExist($element, $class, $default) 
	{
		if ($element->filter($class)->count()) {
			return $element->filter($class)->text(); 
		} else {
			return $default;
		}	
    }

    //SI EXISTE UNA KEY DEVOLVEMOS EL VALUE	
	public function getValueIfExist($array, $key)
	{
		if (array_key_exists($key, $array) AND !empty($array[$key])) {
			return $array[$key];
		} else {
			return NULL;
		}
	}
    
    //QUITA PUNTOS Y OTROS CARÁCTERES Y DEVUELVE EL ENTERO
	public function Integer($value)
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

	public function float($value)
	{
		return (float) str_replace(',', '.', $value);
    }
    




    /*
    |--------------------------------------------------------------------------
    |
    |   API TMDB
    |
    |--------------------------------------------------------------------------
    */

    public function searchTmdbId($faId, $faTitle, $faOriginal, $faYear)
	{

		if (array_key_exists($faId, config('movies.verified'))) {
			return config('movies.verified')[$faId]; //ID DE VERIFICADAS
		}

		$search = $this->apiTmdbId($faTitle, $faYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$search = $this->apiTmdbId($faOriginal, $faYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}
		
		$fwYear = $faYear + 1;
		$search = $this->apiTmdbId($faTitle, $fwYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$search = $this->apiTmdbId($faOriginal, $fwYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$frYear = $faYear - 1;
		$search = $this->apiTmdbId($faTitle, $frYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$search = $this->apiTmdbId($faOriginal, $frYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

    	return null;
    }
    
    public function apiTmdbId($string, $year)
	{
		$api = file_get_contents('https://api.themoviedb.org/3/search/movie?api_key=' . env('TMDB_API_KEY') . '&query=' . urlencode($string) . '&year=' . $year . '&language=es');
		return json_decode($api, true);	
    }
    

    public function updateAllGenres()
    {
    	$api = file_get_contents('https://api.themoviedb.org/3/genre/movie/list?api_key=' . env('TMDB_API_KEY') . '&language=es-ES');
    	$apiGenres = json_decode($api, true);
    	$apiGenres = $this->scraperRepository->updateAllGenres($apiGenres['genres']);
    }
}
