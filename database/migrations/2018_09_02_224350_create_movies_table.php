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
