<?php

namespace App\Http\Controllers\IcScraper;

use App\Models\Character;
use App\Models\Comment;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Param;
use App\Models\User;
use App\Models\MovistarTime;

Use App\Http\Controllers\IcScraper\Images;
use Carbon\Carbon;

class ScraperRepository {

    private $images;

    public function __Construct(Images $images)
    {
        $this->images = $images;
    }

    public function build($data)
    {

        /*
        |--------------------------------------------------------------------------
        | MOVIE
        |--------------------------------------------------------------------------
        */

        $movie = Movie::firstOrNew(['fa_id' => $data['fa_id']]);

        $movie->title            = $data['title'];
        if (!$movie->exists) { /*solo recalculamos slugs para nuevas películas*/
            $movie->slug             = $this->setSlug($data['title']);
        }
        $movie->original_title   = $data['original_title'];
        $movie->country          = $data['country'];
        $movie->duration         = $data['duration'];
        $movie->review           = $data['tm_review'] ? $data['tm_review'] : $data['fa_review'];
        $movie->fa_id            = $data['fa_id'];
        $movie->tm_id            = $data['tm_id'];
        $movie->year             = $data['year'];
        $movie->im_id          = $data['im_id'];
        $movie->rt_url           = array_key_exists('rt_url', $data) ? $data['rt_url'] : null;
        $movie->fa_rat           = $data['fa_rat'];
        $movie->fa_count         = $data['fa_count'];
        $movie->im_rat           = array_key_exists('im_rat', $data) ? $data['im_rat'] : null;
        $movie->im_count         = array_key_exists('im_count', $data) ? $data['im_count'] : null;
        $movie->rt_rat           = array_key_exists('rt_rat', $data) ? $data['rt_rat'] : null;
        $movie->rt_count         = array_key_exists('rt_count', $data) ? $data['rt_count'] : null;

        /*
        |--------------------------------------------------------------------------
        | AVERAGE
        |--------------------------------------------------------------------------
        */

        if ($movie->im_rat) {
            if ($movie->rt_rat) {
                $value = (int)(($movie->im_rat + $movie->rt_rat / 10 + $movie->fa_rat) / 3);
            } else {
                $value = (int)(($movie->im_rat + $movie->fa_rat) / 2);
            }
        } else {
            $value = (int)($movie->fa_rat);
        }
        $movie->avg = $value;

        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */

        $validPoster = $validBackground = '';

        //SI MOVIESCRAPER HA RETORNADO UN POSTER:
        if (isset($data['poster'])) {

            
            //SI LA PELÍCULA YA EXISTE Y TIENE POSTER EN DB
            if (isset($movie->check_poster)) {
                //¿HA CAMBIADO EL NOMBRE DEL FICHERO O ERA NULL ANTES?
                if($movie->check_poster != $data['poster']) {
                    $validPoster = $this->images->savePoster($data['poster'], $movie->slug);
                } 
            //SI ES NUEVA O SI YA EXISTE PERO TENÍA POSTER = NULL
            } else {
                $validPoster = $this->images->savePoster($data['poster'], $movie->slug);
            }

            if ($validPoster == 'saved') {
                $movie->check_poster = 1;
            } elseif ($validPoster == 'error') {
                $movie->check_poster = 0;
            }
        }

        //SI MOVIESCRAPER HA RETORNADO UN BACKGROUND:
        if (isset($data['background'])) {

            //SI LA PELÍCULA YA EXISTE Y TIENE BACKGROUND EN DB
            if (isset($movie->check_background)) {
                if($movie->check_background != $data['background']) {
                    $validBackground = $this->images->saveBackground($data['background'], $movie->slug);
                } 
            //SI ES NUEVA O SI YA EXISTE PERO TENÍA BACKGROUND = NULL
            } else {
                $validBackground = $this->images->saveBackground($data['background'], $movie->slug);
            }

            if ($validBackground == 'saved') {
                $movie->check_background = 1;
            } elseif ($validBackground == 'error') {
                $movie->check_background = 0;
            }
        }

        //GUARDAMOS TODO
        $movie->save();


        /*
        |--------------------------------------------------------------------------
        | CHARACTERS
        |--------------------------------------------------------------------------
        */

        $movie->characters()->detach();

        foreach($data['credits']['cast'] as $i => $cast) {
            //GUARDAMOS ACTOR
            $character = Character::firstOrNew(['id' => $cast['id']]);
            $character->id             = $cast['id'];
            $character->name           = $cast['name'];
            $character->department     = 'actor';
            $character->photo          = $cast['profile_path'];
            $character->save();
            //GUARDAMOS EN ARRAY LISTO PARA SINCRONIZAR DESPUES
            $sync[$cast['id']] = ['order' => $cast['order']];
            //GUARDAMOS IMAGEN SI TIENE
            if ($cast['profile_path']) {
                $this->images->saveCredit($cast['profile_path'], $cast['name'], $movie->id);
            }
        }

        foreach($data['credits']['crew'] as $i => $crew)
        {
            //SOLO GURADAMOS DIRECTOR
            if($crew['job'] == 'Director') {
                $character = Character::firstOrNew(['id' => $crew['id']]);
                $character->id             = $crew['id'];
                $character->name           = $crew['name'];
                $character->department     = 'director';
                $character->photo          = $crew['profile_path'];
                $character->save();
                //GUARDAMOS EN ARRAY LISTO PARA SINCRONIZAR DESPUES
                $sync[$crew['id']] = ['order' => -1];
                //GUARDAMOS IMAGEN SI TIENE
                if ($crew['profile_path']) {
                    $this->images->saveCredit($crew['profile_path'], $crew['name'], $movie->id);
                }
            }
        }

        //SINCRONIZAMOS TABLA PIVOTE
        if (isset($sync)) {
            $movie->characters()->sync($sync);
        }

        /*
        |--------------------------------------------------------------------------
        | GENRES
        |--------------------------------------------------------------------------
        */

        //EXTRAEMOS LA COLUMNA ID DEL ARRAY GENRES
        $values = array_column($data['genres'], 'id');
        if (in_array(10769, $values)) {
            $filter = array(10769);
            $values = array_diff($values, $filter);
        }
        //SINCRONIZAMOS, LOS QUE NO ESTEN EN VALUES SE ELIMINARÁN
        $movie->genres()->sync($values);

        return $movie;
    }

    public function setSlug($slug)
    {
        $slug = str_slug($slug, '-');
        $count = Movie::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        if (empty($count)) return $slug; //si no hay slug retornamos el slug normal

        //ya hay algún slug
        $check = $this->checkSlug($slug, $count);
        if (empty($check)) return "{$slug}-{$count}"; //verificamos nuestro slug y si no existe lo retornamos

        //si nuestro nuevo slug ya existe vamos a ir probando nuevos
        for ($i=1; $i < 10; $i++) { 
            $check = $this->checkSlug($slug, $i);
            if (empty($check)) return "{$slug}-{$i}";
        }
    }

    public function checkSlug($slug, $count)
    {
        return Movie::where('slug', "{$slug}-{$count}")->count();
    }

    //ACTUALIZA TODOS LOS GENEROS
    public function updateAllGenres($apiGenres)
    {
        foreach($apiGenres as $genre) {
            Genre::firstOrCreate(['id' => $genre['id']], ['name' => $genre['name']]);
        }
    }

    public function setParam($name, $value=NULL, $date=NULL) 
    {
        //SI EXISTE UNA FILA CON EL NOMBRE QUE VAMOS A GUARDAR, ANTES LA BORRAMOS
        $old = Param::where('name', $name);
        if ($old->count() > 0) {
            $old->delete();
        }

        $param = New Param;
        $param->name = $name;
        $param->value = $value;
        $param->date = $date;
        $param->save();
    }

    public function getParam($name, $column)
    {
        return Param::where('name', $name)->value($column);
    }

    public function checkIfExist($id)
    {
        $movie = Movie::where('fa_id', $id)->first();
        if ($movie) return true;
        return false;
    }

    public function update($card)
    {
        $movie = Movie::where('fa_id', $card['id'])->first();
        $movie->fa_rat = $card['rat'];
        $movie->fa_count = $card['count'];
        $movie->save();
    }

    public function resetMovistar()
    {
        if (MovistarTime::where('time', '<', Carbon::now()->subHour())->count()) {
            MovistarTime::where('time', '<', Carbon::now()->subHour())->delete();
        }
    }

    public function searchByTitle($title)
    {
        //BUSCAMOS POR TITULO EXACTO
        $movie = Movie::where('title', $title)->get();
        if ($movie->count() == 1) return [$movie->first(), 'Por título exacto'];

        //BUSCAMOS POR TITULO EXACTO SIN PARÉNTESIS
        if (strpos($title, '(') !== FALSE) { 
            $title = trim(preg_replace("/\([^)]+\)/","",$title));
            $movie = Movie::where('title', $title)->get();
            if ($movie->count() == 1) return [$movie->first(), 'Por título exacto y quitando paréntesis'];
        }

        //SI NO SE ENCUENTRA DEVOLVEMOS NULL
        return NULL;
    }

    public function searchByDetails($movistarTitle, $movistarOriginal, $movistarYear)
    {
        $cycle = [$movistarYear - 1, $movistarYear + 1];

        //BUSCAMOS POR LIKE
        $movie = Movie::where('title', 'like', '%' . $movistarTitle . '%')
            ->whereBetween('year', $cycle)
            ->get();
        if ($movie->count() == 1) return [$movie->first(), 'Por like y año +-1.'];

        //SI HAY PARÉNTESIS LOS QUITAMOS Y VOLVEMOS A BUSCAR
        if (strpos($movistarTitle, '(') !== FALSE) { 
            $movistarTitleNoBrackets = trim(preg_replace("/\([^)]+\)/","",$movistarTitle));
            $movie = Movie::where('title', 'like', '%' . $movistarTitleNoBrackets . '%')
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return [$movie->first(), 'Por like, año +-1 y quitando parentesis'];
        }

        //SI NO BUSCAMOS POR EXACTO
        $movie = Movie::where('title', $movistarTitle)
            ->whereBetween('year', $cycle)
            ->get();
        if ($movie->count() == 1) return [$movie->first(), 'Por título exacto y año +-1'];

        if($movistarOriginal && $movistarOriginal != $movistarTitle) {
            //SI NO BUSCAMOS POR ORIGINAL CON LIKE
            $movie = Movie::where('original_title', 'like', '%' . $movistarOriginal . '%')
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return [$movie->first(), 'Por like con título original y año +-1'];

            //SI NO BUSCAMOS POR ORIGINAL EXACTO
            $movie = Movie::where('original_title', $movistarOriginal)
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return [$movie->first(), 'Por titulo original exacto y año +-1'];
        }

        //SI NO SE ENCUENTRA DEVOLVEMOS NULL
        return NULL;

    }


    public function setMovie($movie, $datetime, $channelCode, $channel)
    {
        $match = MovistarTime::where([['movie_id', '=', 36123],['time', '=', $datetime]])->first();
        if ($match) return;
        MovistarTime::insert(
            ['time' => $datetime, 'channel' => $channel, 'channel_code' => $channelCode, 'movie_id' => $movie->id]
        );
    }

    /* public function getMovistarValidDate($date)
    {
        $match = MovistarTime::whereDate('time', $date)->count();
        dd($match);
        if ($match > 50) return false;
        return true;
    } */

    /*
    |--------------------------------------------------------------------------
    | AVERAGES
    |--------------------------------------------------------------------------
    */


    /* public function stars($value)
    {
        switch (true) {
            case ($value >= 8): return 5;
            case ($value >= 7): return 4;
            case ($value >= 6): return 3;
            case ($value >= 5): return 2;
            case ($value >= 4): return 1;
            default: return 0;
        }
    } */


}
