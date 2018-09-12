<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Movie;
use App\Models\Genre;

class CreateGenresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->integer('id')->unsigned()->primary();
            $table->string('name');
        });

        Schema::create('genre_movie', function (Blueprint $table) {
            $table->integer('movie_id')->unsigned();
            $table->foreign('movie_id')->references('id')->on('movies');
            $table->integer('genre_id')->unsigned();
            $table->foreign('genre_id')->references('id')->on('genres');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('genres')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        DB::table('genres')->insert([
            ['id' => 12, 'name' => 'Aventura'], 
            ['id' => 14, 'name' => 'Fantasía'],
            ['id' => 16, 'name' => 'Animación'],
            ['id' => 18, 'name' => 'Drama'],
            ['id' => 27, 'name' => 'Terror'],
            ['id' => 28, 'name' => 'Acción'],
            ['id' => 35, 'name' => 'Comedia'],
            ['id' => 36, 'name' => 'Historia'],
            ['id' => 53, 'name' => 'Suspense'],
            ['id' => 80, 'name' => 'Crimen'],
            ['id' => 99, 'name' => 'Documental'],
            ['id' => 878, 'name' => 'Ciencia Ficción'],
            ['id' => 9648, 'name' => 'Misterio'],
            ['id' => 10402, 'name' => 'Música'],
            ['id' => 10749, 'name' => 'Romance'],
            ['id' => 10751, 'name' => 'Familia'],
            ['id' => 10752, 'name' => 'Guerra']
        ]);

        $movies = Movie::pluck('id');
        $genres = Genre::pluck('id');
        foreach ($movies as $movie) {
            //por cada pelicula haya un genero
            DB::table('genre_movie')->insert(['genre_id' => $genres->random(), 'movie_id' => $movie]);
        }
        //mas unos cuantos mas aleatorios
        for ($i=0; $i < 20; $i++) { 
            DB::table('genre_movie')->insert(['genre_id' => $genres->random(), 'movie_id' => $movies->random()]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genre_movie');
        Schema::dropIfExists('genres');
    }
}
