<?php
namespace App\Http\Controllers\IcScraper;

use Image;

class Images 
{

	public function savePoster($file, $slug) 
	{
		try {
			$url = 'http://image.tmdb.org/t/p/w1280' . $file;
			Image::make($url)->fit(86, 129)->save(public_path() . '/assets/movieimages/posters/std/' . $slug . '.jpg');
			Image::make($url)->fit(150, 225)->save(public_path() . '/assets/movieimages/posters/lrg/' . $slug . '.jpg');
			Image::make($url)->fit(30, 45)->save(public_path() . '/assets/movieimages/posters/sml/' . $slug . '.jpg');
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
			Image::make($url)->fit(800, 450)->save(public_path() . '/assets/movieimages/backgrounds/std/' . $slug . '.jpg');
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
			Image::make($url)->save(public_path() . '/assets/movieimages/credits' . $file);
			return 'saved';
		} catch (\Exception $e) {
			$log = new \Monolog\Logger(__METHOD__);
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path().'/logs/scrapingerrors.log'));
			$log->addInfo($name . ' : Error al guardar la foto de perfil en intevention, en ' .$url . ' ' . $movie_id);
			return 'error';
		}		
	}

   	public function setImageList($posters, $listId)
	{
		$img = Image::canvas(166, 248, '#202326');
		$image1 = Image::make(public_path() . '/assets/posters/medium' . $posters[0])->resize(83, 124);
		$image2 = Image::make(public_path() . '/assets/posters/medium' . $posters[1])->resize(83, 124);
		$image3 = Image::make(public_path() . '/assets/posters/medium' . $posters[2])->resize(83, 124);
		$image4 = Image::make(public_path() . '/assets/posters/medium' . $posters[3])->resize(83, 124);
		$img->insert($image1, 'top-left');
		$img->insert($image2, 'top-right');
		$img->insert($image3, 'bottom-left');
		$img->insert($image4, 'bottom-right');
		$img->save(public_path() . '/assets/imagelists/' . $listId . '.jpg');
	}

}
