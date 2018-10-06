<?php

namespace App\Http\Controllers\IcScraper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Goutte\Client;
use Carbon\Carbon;

use App\Models\MovistarLog;
use App\Http\Controllers\IcScraper\ScraperRepository;

class TimesScraper extends Controller
{

    private $scraperRepository;

    public function __Construct(ScraperRepository $scraperRepository)
	{
		$this->scraperRepository = $scraperRepository;
	}

    public function Movistar()
    {
        //borramos times ya pasados
        $this->scraperRepository->resetMovistar();
        echo 'borrada programacion antigua...' . "<br>";

        $client = new Client();

        $daysToScrap = $this->daysToScrap();

        if (!$daysToScrap) {
            echo 'Todo está descargado';
            return;
        }

        foreach ($daysToScrap as $dayToScrap) {
            echo 'descargando fecha:' . $dayToScrap . '...' . "<br>";
            foreach (config('movies.channels') as $channelCode => $channel) {
                echo $channelCode . " ";
                $url = 'http://www.movistarplus.es/guiamovil/' . $channelCode . '/' . $dayToScrap;
                $crawler = $client->request('GET', $url);
                if ($client->getResponse()->getStatus() !== 200) echo $url . ' devuelve error ' . $client->getResponse()->getStatus();
                $this->scrapPage($client, $crawler, $dayToScrap, $channelCode, $channel);
            }
            echo "<br>" . 'Finalizado.' . "<br>";
            $this->scraperRepository->setParam('Movistar', Null, $dayToScrap);
        }
    }

    public function scrapPage($client, $crawler, $date, $channelCode, $channel)
    {
        //RECORREMOS FILAS
		$crawler->filter('.container_box.g_CN')->each(function($node, $i) use($client, $date, $channelCode, $channel) {
            
            //SI NO ES CINE DESCARTAMOS
            if ($node->filter('li.genre')->text() != 'Cine') return;

            $title = trim($node->filter('li.title')->text());
            $time = $node->filter('li.time')->text();
            $datetime = $this->movistarDate($time, $date);
            $splitDay = $this->splitDay($date); //6 DE LA MAÑANA DEL DIA $DATE

            //SI LA HORA DE LA PELICULA ES ANTES DE LAS 6:00 (SPLITTIME) Y LA FILA DE LA TABLA ES DESPUES DE LA FILA 6, AÑADIMOS UN DÍA
			if ($datetime < $splitDay && $i > 6) {
				$datetime = $datetime->addDay();
            }
            
            //ANULAMOS SI EL TITULO COINCIDE CON FRASES BANEADAS
			foreach(config('movies.moviesTvBan') as $ban) {
				if (strpos($title, $ban) !== FALSE) {
                    MovistarLog::create(['movistar_title' => $title, 'datetime' => $datetime, 'channel' => $channel, 'valid' => 0, 'comment' => 'Baneada al encontrarse en la lista moviesTvBan']);
					return;
				}
            }
            
            //BORRAMOS PALABRAS BANEADAS DEL TITULO
            $title = str_replace(config('movies.wordsTvBan'), '', $title);
            
            //BUSCAMOS 1 COINCIDENCIA POR TITULO EXACTO
            $movie = $this->scraperRepository->searchByTitle($title);
            
            if ($movie) {

                MovistarLog::create(['movistar_title' => $title, 'fa_title' => $movie[0]->title, 'fa_original' => $movie[0]->original_title, 'datetime' => $datetime, 'channel' => $channel, 'valid' => 1, 'comment' => 'Encontrada sin entrar: ' . $movie[1]]);
                $this->scraperRepository->setMovie($movie[0], $datetime, $channelCode, $channel);
                return;

            } 

            //SI NO LA ENCONTRAMOS ENTRAMOS EN LA FICHA
            
			$page = $client->click($node->filter('a')->link());

            //ALGUNAS FICHAS DE 'CINE CUATRO', 'CINE BLOCKBUSTER',.. SIN PELICULA, NO TIENEN AÑO EN LA FICHA, ANULAMOS
            if ($page->filter('p[itemprop=datePublished]')->count() == 0) {
                MovistarLog::create(['movistar_title' => $title, 'datetime' => $datetime, 'channel' => $channel, 'valid' => 0, 'comment' => 'Baneada entrando, al no tener año dentro de la ficha de la película.']);
                return;
            }

            //ANULAMOS CUALQUIER PELICULA SIN DURACIÓN
            if ($page->filter('span[itemprop=duration]')->count() == 0) {
                MovistarLog::create(['movistar_title' => $title, 'datetime' => $datetime, 'channel' => $channel, 'valid' => 0, 'comment' => 'Baneada entrando, al no tener duración en la etiqueta itemprop']);
                return;
            }
            //ANULAMOS CUALQUIER PELÍCULA CON DURACIÓN DEMASIADO CORTA
            $duration = $page->filter('span[itemprop=duration]')->text();
            $duration = explode(':', $duration);
            $minutes = $duration[0] * 60 + (int)$duration[1];
            if ($minutes < 60) {
                MovistarLog::create(['movistar_title' => $title, 'datetime' => $datetime, 'channel' => $channel, 'valid' => 0, 'comment' => 'Baneada entrando, al no tener una duración inferior a 1 hora']);
                return;
            }

            //COJEMOS DATOS
            $year = $page->filter('p[itemprop=datePublished]')->attr('content');
            $original = $this->getElementIfExist($page, '.title-especial p', NULL);

            //BUSCAMOS CON LOS DATOS
            $movie = $this->scraperRepository->searchByDetails($title, $original, $year);

            if ($movie) {
                MovistarLog::create(['movistar_title' => $title, 'movistar_original' => $original, 'fa_title' => $movie[0]->title, 'fa_original' => $movie[0]->original_title, 'datetime' => $datetime, 'channel' => $channel, 'valid' => 1, 'comment' => 'Encontrada entrando, por detalles: ' . $movie[1]]);
                $this->scraperRepository->setMovie($movie[0], $datetime, $channelCode, $channel);
                return;
            }
            
            MovistarLog::create(['movistar_title' => $title, 'movistar_original' => $original, 'datetime' => $datetime, 'channel' => $channel, 'valid' => 0, 'comment' => 'No encontrada ni entrando']);

        });
    }

    public function daysToScrap()
    {
        //compara la fecha del último scraper con la actual y devuelve un array con las fechas que hay que scrapear
        $lastDayScraped = $this->scraperRepository->getParam('Movistar', 'date');
        $lastDayScraped = $lastDayScraped->format('Y-m-d');
        $today = Carbon::now()->toDateString();
        $tomorrow = Carbon::now()->addDay()->toDateString();
        if ($today > $lastDayScraped) {
            return [$today, $tomorrow];
        } elseif ($tomorrow > $lastDayScraped) {
            return [$tomorrow];
        } else {
            return;
        }
    }

    //$date = 'YYYY-MM-DD'; $time = 09:16;
    public function movistarDate($time, $date)
    {
    	$time = $this->cleanData($time);
    	$time = explode(':', $time);
    	$date = explode('-', $date);
		//año, mes, dia, hora, minuto, segundo, timezone
		return Carbon::create($date[0], $date[1], $date[2], $time[0], $time[1]);
    }

    public function splitDay($date)
    {
    	$date = explode('-', $date);
    	return Carbon::create($date[0], $date[1], $date[2], 6, 00);
    }

    //LIMPIAMOS EL STRING DE ESPACIOS, SALTOS DE LINEA,...
	public function cleanData($value)
	{
		$value = preg_replace('/\xA0/u', ' ', $value); //Elimina %C2%A0 del principio y resto de espacios
		$value = trim(str_replace(array("\r", "\n"), '', $value)); //elimina saltos de linea al principio y final
		return $value;
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

}