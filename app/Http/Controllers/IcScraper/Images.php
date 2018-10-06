<?php
namespace App\Http\Controllers\IcScraper;

use Image;

class Images 
{

	public function savePoster($file, $slug) 
	{
		try {
			$url = 'http://image.tmdb.org/t/p/w1280' . $file;
			Image::make($url)->fit(86, 129)->save(public_path() . '/movimages/posters/std/' . $slug . '.jpg');
			Image::make($url)->fit(150, 225)->save(public_path() . '/movimages/posters/lrg/' . $slug . '.jpg');
			Image::make($url)->fit(30, 45)->save(public_path() . '/movimages/posters/sml/' . $slug . '.jpg');
			return 'saved';
		} catch (\Exception $e) {
			$log = new \Monolog\Logger(__METHOD__);
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path().'/logs/scrapingerrors.log'));
			$log->addInfo($slug . ' : Error al guardar el poster en intervention');
			return 'error';
		}
	}

	public function saveBackground($file, $slug) 
	{
		try {
			$url = 'http://image.tmdb.org/t/p/w1280' . $file;
			Image::make($url)->fit(800, 450)->save(public_path() . '/movimages/backgrounds/std/' . $slug . '.jpg');
			return 'saved';
		} catch (\Exception $e) {
			$log = new \Monolog\Logger(__METHOD__);
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path().'/logs/scrapingerrors.log'));
			$log->addInfo($slug . ' : Error al guardar el background en intevention');
			return 'error';
		}
	}

	public function saveCredit($file, $name, $movie_id)
	{
		try {
			$url = 'http://image.tmdb.org/t/p/w185' . $file;
			Image::make($url)->save(public_path() . '/movimages/credits' . $file);
			return 'saved';
		} catch (\Exception $e) {
			$log = new \Monolog\Logger(__METHOD__);
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path().'/logs/scrapingerrors.log'));
			$log->addInfo($name . ' : Error al guardar la foto de perfil en intevention, en ' .$url . ' ' . $movie_id);
			return 'error';
		}		
	}


}
