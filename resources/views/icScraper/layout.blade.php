<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IcScrap</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/icscrap.css') }}" rel="stylesheet">
</head>
<body>
    <header>
        <div class="wrap">
            <div class="top-bar">
                <h1 class="title">IcScrap</h1>
                <ul class="nav-main">
                    <li> {{ Auth::user()->name }}</li>
                    <li> <a href="{{ route('logout') }}">Salir</a> </li>
                </ul>
            </div>
        </div>
    </header>
    <main>
        <div class="wrap">
            <div class="content">
                @yield('content')
            </div>
        </div>
    </main>
    </div>
</body>
</html>
