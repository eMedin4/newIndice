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
	
	/* 
		ACCESSORS
	*/

	public function getStarsAttribute()
	{

		switch (true) {
			case ($this->avg >= 7): return '
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>';
			case ($this->avg >= 6): return '
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-nocolor"></span>';
            case ($this->avg >= 5): return '
				<span class="icon-star-full star-3-color"></span>
				<span class="icon-star-full star-3-color"></span>
				<span class="icon-star-full star-3-color"></span>
				<span class="icon-star-full star-3-nocolor"></span>
				<span class="icon-star-full star-3-nocolor"></span>';
            case ($this->avg >= 4): return '
				<span class="icon-star-full star-2-color"></span>
				<span class="icon-star-full star-2-color"></span>
				<span class="icon-star-full star-2-nocolor"></span>
				<span class="icon-star-full star-2-nocolor"></span>
				<span class="icon-star-full star-2-nocolor"></span>';
            default: return '
				<span class="icon-star-full star-1-color"></span>
				<span class="icon-star-full star-1-nocolor"></span>
				<span class="icon-star-full star-1-nocolor"></span>
				<span class="icon-star-full star-1-nocolor"></span>
				<span class="icon-star-full star-1-nocolor"></span>';
        } 
	}

	public function getExcerpt100Attribute()
	{
		return str_limit($this->review, 100, '...');
	}

	public function getExcerpt400Attribute()
	{
		return str_limit($this->review, 400, '...');
	}

	public function getExcerpt200Attribute()
	{
		return str_limit($this->review, 200, '...');
	}

}
