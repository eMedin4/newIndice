<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Movie;
use Carbon\Carbon;

class CreateTvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('time');
            $table->string('channel');
            $table->string('channel_code');
            $table->integer('movie_id')->unsigned();
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
            $table->timestamps();
        });


        $tvs = [];
        $movies = Movie::pluck('id');
        $minutes = [0, 0, 0, 30, 30, 15, 45];
        for ($i=1; $i < 100; $i++) { 
            $tv = [
                'time' => Carbon::now()->addHours(rand(1,24))->minute($minutes[array_rand($minutes)])->second(0),
                'channel' => 'canal',
                'channel_code' => 'channel_code',
                'movie_id' => $movies->random()
            ];
            array_push($tvs, $tv);
        }
        $test2 = DB::table('tv')->insert($tvs);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tv');
    }
}
