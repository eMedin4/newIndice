php a<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('original_title');
            $table->mediumInteger('fa_id');
            $table->mediumInteger('tm_id');
            $table->smallInteger('year');
            $table->smallInteger('duration');
            $table->string('country');
            $table->text('review');
            $table->boolean('check_poster')->default(0);
            $table->boolean('check_background')->default(0);
            $table->string('imdb_id')->nullable();
            $table->string('rt_url')->nullable();
            $table->tinyInteger('avg');
            $table->decimal('fa_rat', 3, 1)->nullable();
            $table->mediumInteger('fa_count')->nullable();
            $table->decimal('im_rat', 3, 1)->nullable();
            $table->mediumInteger('im_count')->nullable();
            $table->integer('rt_rat')->nullable();
            $table->mediumInteger('rt_count')->nullable();
            $table->timestamps();
        });


        $title = ['Puro Vicio','Under the Skin', 'Solo Dios Perdona', 'Enemy', 'El Arbol de la Vida', 'Origen', 'Cisne Negro', 'Anticristo', 'La fuente de la vida', '!Olvidate de Mi!', 'Donnie Darko', 'El Viaje de Chihiro', 'Abre los Ojos', 'Carretera Perdida', 'Blade Runner', 'La Montaña Sagrada', 'Easy Rider (Buscando mi Destino)', '2001: Una Odisea del Espacio', 'El Año Pasado en Marienbad'];
        $movies = [];
        foreach ($title as $t) {
            $slug = str_slug($t);
            $movie = [
                'title' => $t, 
                'slug' => $slug,
                'original_title' => $t,
                'fa_id' => '1',
                'tm_id' => '1',
                'year' => rand(1960,2018),
                'duration' => rand(90,170),
                'country' => 'Estados Unidos',
                'review' => 'La historia está ambientada a finales de los años sesenta en Los Ángeles. Doc Sportello es un detective privado que hace mucho tiempo que no ve a su ex novia Shasta, hasta que un día ésta contrata sus servicios para resolver la desaparición de su nuevo amante. Sportello se verá entonces enredado en una serie de situaciones donde dejará atrás los escrúpulos y donde la resolución del misterio no será finalmente el objetivo principal.',
                'check_poster' => 1,
                'check_background' => 1,
                'imdb_id' => '1',
                'rt_url' => '1',
                'avg' => rand(30,90)/10,
                'fa_rat' => rand(30,90)/10,
                'fa_count' => rand(100,10000),
                'im_rat' => rand(30,90)/10,
                'im_count' => rand(100,10000),
                'rt_rat' => rand(30,90),
                'rt_count' => 0,
            ];
            array_push($movies, $movie);
        }
        DB::table('movies')->insert($movies);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
