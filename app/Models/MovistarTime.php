<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class MovistarTime extends Model
{
	
	public $timestamps = false;
	protected $dates = ['time'];
	
	public $now, $today21, $tomorrow02, $tomorrow14, $tomorrow21, $aftertomorrow02;

	public function __Construct()
	{
		$this->now = Carbon::now();
		$this->today21 = Carbon::now()->setTime(21,0,0);
		if ($this->now > $this->today21) $this->today21 = $this->now->subHour();
		$this->tomorrow02 = Carbon::now()->addDay()->setTime(2,0,0);
		$this->tomorrow14 = Carbon::now()->addDay()->setTime(14,0,0);
		$this->tomorrow21 = Carbon::now()->addDay()->setTime(21,0,0);
		$this->aftertomorrow02 = Carbon::now()->addDay(2)->setTime(2,0,0);
	}
	
    public function movie()
	{
		return $this->belongsTo(Movie::class);
	}

	public function getFormatTimeAttribute()
    {
    	if ($this->time < $this->now) {
    		return '<time class="tv-alert"><span>En emisión</span>Hace ' . $this->time->diffInMinutes($this->now) . '<span> m.</span></time>';
    	} elseif ($this->time->isToday()) {
    		return '<time>Hoy a las ' . $this->time->format('G:i') . '</time>';
    	} else {
    		return '<time>' . $this->time->format('G:i') . ' <span>' . $this->time->formatLocalized('%a') . '</span></time>';
		}
	}

	public function getDayPartingAttribute()
	{
		switch (true) {
			case ($this->time->between($this->now->subHour(), $this->today21)): return ['help' => 'entre ahora y las 21', 'coeficient' => 0.8];
			case ($this->time->between($this->today21, $this->tomorrow02)): return ['help' => 'entre las 21 y las 02', 'coeficient' => 1];
			case ($this->time->between($this->tomorrow02, $this->tomorrow14)): return ['help' => 'entre las 02 y las 14 de mañana', 'coeficient' => 0.5];
			case ($this->time->between($this->tomorrow14, $this->tomorrow21)): return ['help' => 'entre las 14 y las 21 de mañana', 'coeficient' => 0.6];
			case ($this->time->between($this->tomorrow21, $this->aftertomorrow02)): return ['help' => 'entre las 21 y las 02 de mañana', 'coeficient' => 0.7];
            default: return ['help' => 'a partir de las 2 de pasado mañana', 'coeficient' => 0.4];
        } 
	}

	public function getSortCoeficientAttribute()
	{
		return $this->movie->fa_popularity['step1'] * $this->day_parting['coeficient'];
	}

	
	
}
