<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lo Mejor de la TV</title>
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700|Roboto:400,500,700,900" rel="stylesheet">


</head>
<body>
	<div class="header">
		<div class="main-wrap header-wrap">
			<div class="title">
                <span class="icon-tv"></span>
				<p>lomejordelatv</p>
            </div>
            <nav class="section-links">
                <a href="">Televisión</a>
                <a href="">Netflix</a>
                <a href="">HBO</a>
                <a href="">Amazon</a>
                <a href="">Itunes</a>
            </nav>
		</div>
	</div>
    <div class="main">
    	<div class="main-wrap">
            @yield('content')
    	</div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="{{ asset('/js/scripts.js') }}"></script>
    @yield ('scripts')
    

</body>
</html>