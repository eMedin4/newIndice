<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Param extends Model
{

	public $timestamps = false;

	//COLUMNAS QUE TRATA CON CARBON AUTOMÁTICAMENTE
	public function getDates()
	{
	    return ['date'];
	}

}
