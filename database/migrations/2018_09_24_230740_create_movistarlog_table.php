<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovistarlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movistar_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('movistar_title');
            $table->string('movistar_original')->nullable();
            $table->string('fa_title')->nullable();
            $table->string('fa_original')->nullable();
            $table->dateTime('datetime');
            $table->string('channel');
            $table->boolean('valid');
            $table->string('comment');
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
        Schema::dropIfExists('movistar_logs');
    }
}
