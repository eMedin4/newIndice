<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,900" rel="stylesheet">


</head>
<body>
    <div class="top-bar">
    	<div class="wrap">Top-Bar</div>
    </div>
    <div class="main">
    	<div class="wrap">
			<div class="main-layout">
				<div class="col-1">
					@foreach ($relatedMovies as $movie)
						<article class="relevant-movie">
							<div class="thumb">
								<img src="{{ asset('/movieimages/backgrounds/std/' . $movie->slug . '.jpg') }}" alt="">
								<div class="darker"></div>
							</div>
							<div class="info">
								<h3 class="h3">{{ $movie->title }}</h3>
								<div class="meta">
									<div class="stars">{!! $movie->stars !!}</div>
									{{ $movie->country }} · {{ $movie->duration }} ·
									@foreach ($movie->genres as $genre)
										{{ $loop->first ? '' : ', ' }}
										<span>{{ $genre->name }}</span>
									@endforeach
								</div>
								<p>{{ $movie->excerpt200 }}</p>
								<div class="task">
									<span>Disney Channel</span><time>hoy a las 23:45h</time>
								</div>
							</div>
						</article>
					@endforeach
				</div>
				<div class="col-2">
					@foreach ($movies as $movie)
						<article class="{{ $loop->first ? 'featured-movie' : 'movie' }}">
							<div class="thumb">
								<img src="{{ asset('/movieimages/backgrounds/std/' . $movie->slug . '.jpg') }}" alt="">
								<div class="darker"></div>
								
							</div>
							<div class="info">
								<h3 class="{{ $loop->first ? 'h2' : 'h4' }}">{{ $movie->title }}</h3>
								<div class="meta">
									<div class="stars">{!! $movie->stars !!}</div>
									{{ $movie->country }} · {{ $movie->duration }} ·
									@foreach ($movie->genres as $genre)
										{{ $loop->first ? '' : ', ' }}
										<span>{{ $genre->name }}</span>
									@endforeach
								</div>
								{!! $loop->first ? '<p>' . $movie->excerpt400 . '<p>' : '' !!}</p>
								<div class="task">
									<span>Disney Channel</span><time>hoy a las 23:45h</time>
								</div>
							</div>
						</article>
					@endforeach

				</div>

				<div class="col-3">
					<div class="box-test"></div>
				</div>
			</div>
    	</div>
    </div>
</body>
</html>