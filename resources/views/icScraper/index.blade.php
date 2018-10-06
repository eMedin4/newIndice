@extends('icScraper.layout')

@section('content')

    Yeahhh 111

    <table class="table">
        <thead>
          <tr>
            <th>id</th>
            <th>fa_id</th>
            <th>tm_id</th>
            <th>im_id</th>
            <th>title</th>
            <th>slug</th>
            <th>original</th>
            <th>year</th>
            <th>duration</th>
            <th>country</th>
            <th>poster</th>
            <th>backg</th>
            <th>avg</th>
            <th>created</th>
            <th>updated</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($movies as $movie)
                <tr>
                    <td>{{$movie->id}}</td>
                    <td>{{$movie->fa_id}}</td>
                    <td>{{$movie->tm_id}}</td>
                    <td>{{$movie->imdb_id}}</td>
                    <td>{{$movie->title}}</td>
                    <td>{{$movie->slug}}</td>
                    <td>{{$movie->original_title}}</td>
                    <td>{{$movie->year}}</td>
                    <td>{{$movie->duration}}</td>
                    <td>{{$movie->country}}</td>
                    <td>{{$movie->check_poster}}</td>
                    <td>{{$movie->check_background}}</td>
                    <td>{{$movie->avg}}</td>
                    <td>{{$movie->created_at}}</td>
                    <td>{{$movie->updated_at}}</td>
                </tr>
            @endforeach
        </tbody>
      </table>

      {{ $movies->links() }}

    <h2>Introduce el id de Filmaffinity:</h2>
	    <form method="GET" action="">
		{!! csrf_field() !!}
		<div class="inline-block">
			<input type="text" name="id" value="{{old('id')}}" placeholder="id">
		</div>
		<button class="btn" type="submit">Procesar id de Filmaffinity</button>
	</form>

	<h2>Scrapea películas en cartelera</h2>
	<a class="btn" href="">Iniciar cartelera de Filmaffinity</a>

	<h2>Scrapea guia Movistar</h2>
	<a class="btn" href="">Iniciar programación Movistar</a>

	<h2>Scrapea guia Movistar por url</h2>
	<form method="GET" action="">
		{!! csrf_field() !!}
		<div class="inline-block">
			<input type="text" name="url" value="{{old('url')}}" placeholder="url">
		</div>
		<button class="btn" type="submit">Iniciar URL de Movistar</button>
	</form>

	<h2>Scrapea todas las películas:</h2>
	<form method="GET" action="">
		{!! csrf_field() !!}
		<div class="inline-block">
			<input type="text" name="letter" value="{{old('letter')}}" placeholder="Letra">
			<input type="text" name="first-page" value="{{old('first-page')}}" placeholder="Pag inicio">
			<input type="text" name="total-pages" value="{{old('total-pages')}}" placeholder="Pags totales">
		</div>
		<button type="submit" class="btn">Procesar páginas de Filmaffinity</button>
	</form>

@endsection