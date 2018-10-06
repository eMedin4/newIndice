@extends('layouts.layout')

@section('content')

	<div class="movie-layout">

		<div class="col-1">
            <div class="poster">
                <img src="{{ asset('/movimages/posters/std/' . $record->slug . '.jpg') }}" alt="">
            </div>
		</div>

		<div class="col-2">
            <article class="single-movie">
                <div class="thumb">
                    <img src="{{ asset('/movimages/backgrounds/std/' . $record->slug . '.jpg') }}" alt="">
                    <div class="darker"></div>
                </div>
                <div class="info">
                    <h1 class="h1">{{ $record->title }}</h1>
                    <ul class="meta">
                        <li class="country country-{{ $record->country }}"></li>
                        <li class="year">{{ $record->year }}</li>
						<li class="break"></li>
                        <li>{{ $record->original_title }}</li>
                        <li class="break"></li>
                        <li>{{ $record->duration }}</li>
                        <li class="break"></li>
                        <li>
                            @foreach ($record->genres as $genre)
                                {{ $loop->first ? '' : ', ' }}
                                <span>{{ $genre->name }}</span>
                            @endforeach
                        </li>
                    </ul>
                    <p>{{ $record->review }}</p>
                </div>
                <div class="ratings">
                    @if ($record->fa_rat)
                        <ul class="rating">
                            <li class="source"><p>Filmaffinity</p></li>
                            <li class="stars"><div class="rat">{{ $record->fa_rat }}</div>{!! $record->fa_stars !!}</li>
                            <li>
                                <ul class="popularity-list">
                                    <li class="popularity-tag">POPULAR</li>
                                    <li class="popularity popularity-{{ $record->fa_popularity["class"] }}"><span class="popularity-inner"></span></li>
                                    <li class="count">{{ $record->fa_count }}</li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                    @if ($record->im_rat)
                        <ul class="rating">
                            <li class="source"><p>IMDB</p></li>
                            <li class="stars"><div class="rat">{{ $record->im_rat }}</div>{!! $record->im_stars !!}</li>
                            <li>
                                <ul class="popularity-list">
                                    <li class="popularity-tag">POPULAR</li>
                                    <li class="popularity popularity-{{ $record->im_popularity["class"] }}"><span class="popularity-inner"></span></li>
                                    <li class="count">{{ $record->im_count }}</li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                    @if ($record->rt_rat)
                        <ul class="rating">
                            <li class="source"><p>Rotten Tomattoes</p></li>
                            <li class="stars"><div class="rat">{{ $record->rt_rat }}</div>{!! $record->rt_stars !!}</li>
                            <li>
                                <ul class="popularity-list">
                                    <li class="popularity-tag">POPULAR</li>
                                    <li class="popularity popularity-{{ $record->rt_popularity["class"] }}"><span class="popularity-inner"></span></li>
                                    <li class="count">{{ $record->rt_count }}</li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                </div>
            </article>
		</div>

	</div>

@endsection