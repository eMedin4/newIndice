<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

	/* protected $guarded = []; */

	protected $fillable = ['fa_id'];
    
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

	public function getAvgStarsAttribute()
	{
		return $this->stars($this->avg);		
	}

	public function getFaStarsAttribute()
	{
		return $this->stars($this->fa_rat);
	}

	public function getImStarsAttribute()
	{
		return $this->stars($this->im_rat);
	}

	public function getRtStarsAttribute()
	{
		return $this->stars($this->rt_rat);
	}

	public function stars($value)
	{
		switch (true) {
			case ($value >= 7): return '
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>
				<span class="icon-star-full star-5-color"></span>';
			case ($value >= 6): return '
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-color"></span>
				<span class="icon-star-full star-4-nocolor"></span>';
            case ($value >= 5): return '
				<span class="icon-star-full star-3-color"></span>
				<span class="icon-star-full star-3-color"></span>
				<span class="icon-star-full star-3-color"></span>
				<span class="icon-star-full star-3-nocolor"></span>
				<span class="icon-star-full star-3-nocolor"></span>';
            case ($value >= 4): return '
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

	public function getFaPopularityAttribute()
	{
        return $this->popularityAlgorithm($this->year, $this->fa_count, 'fa');
	}

	public function getImPopularityAttribute()
	{
		return $this->popularityAlgorithm($this->year, $this->im_count, 'im');
	}

	public function popularityAlgorithm($year, $count, $source)
	{

		//establecemos los máximos
		if ($source == 'fa') {
			$source_highest = 50000;
		} else if ($source == 'im') {
			$source_highest = 200000;
		} else {
			return 'error en algoritmo';
		}

		//step1 : convertimos el count en relativo a 10
		if ($count > $source_highest) $count = $source_highest;
		$step1 = $count/($source_highest/10);
		$step1 = round($step1, 1);
		
		//step2 : calculamos el coeficiente de año y multiplicamos
		$yearMax = 2019;
		$yearMin = 1999;
		$yearCoefMax = 1.5;
		$yearCoefMin = 0.5;
		if ($year < $yearMin) $year = $yearMin;
		$yearCoef = ((($year - $yearMin) * ($yearCoefMax - $yearCoefMin)) / ($yearMax - $yearMin)) + $yearCoefMin;
		$yearCoef = round($yearCoef, 1);
		$step2 = round($step1 * $yearCoef, 1);
		if ($step2 > 10) $step2 = 10;

		//class: calculamos la clase para css, de 0 a 5
		$class = (int)($step2 / 2);
		
		return [
			"step1" => $step1, 
			"step2" => $step2, 
			"class" => $class
		];
		
	}

	public function getRelationFaImAttribute()
	{
		if ($this->fa_count && $this->im_count) {
			return (int)($this->im_count / $this->fa_count);
		} else {
			return 'sin datos';
		}
	}

}
