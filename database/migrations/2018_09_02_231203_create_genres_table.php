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
