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
					<article class="top">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[5]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3 class="h3">{{ $movies[5]->title }}</h3>
							<p>{{ $movies[5]->review }}</p>
						</div>
					</article>
					<article class="top">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[6]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3 class="h3">{{ $movies[6]->title }}</h3>
							<p>{{ $movies[6]->review }}</p>
						</div>
					</article>
					<article class="top">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[7]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3 class="h3">{{ $movies[7]->title }}</h3>
							<p>{{ $movies[7]->review }}</p>
						</div>
					</article>
				</div>
				<div class="col-2">
					<article class="feature">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[0]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3 class="h2">{{ $movies[0]->title }}</h3>
							<p>{{ $movies[0]->review }}</p>
						</div>
					</article>
					<article class="item">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[1]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3>{{ $movies[1]->title }}</h3>
						</div>
					</article>
					<article class="item">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[2]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3>{{ $movies[2]->title }}</h3>
						</div>
					</article>
					<article class="item">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[3]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3>{{ $movies[3]->title }}</h3>
						</div>
					</article>
					<article class="item">
						<div class="thumb">
							<img src="{{ asset('/movieimages/backgrounds/std/' . $movies[4]->slug . '.jpg') }}" alt="">
						</div>
						<div class="info">
							<h3>{{ $movies[4]->title }}</h3>
						</div>
					</article>
				</div>

				<div class="col-3">
					<div class="box-test"></div>
				</div>
			</div>
    	</div>
    </div>
</body>
</html>