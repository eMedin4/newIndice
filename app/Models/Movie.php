<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

	protected $guarded = [];
    
	public function genres()
	{
		return $this->belongsToMany(Genre::class);
	}

	public function characters()
    {
    	return $this->belongsToMany(Character::class)->withPivot('order');
    }

}
