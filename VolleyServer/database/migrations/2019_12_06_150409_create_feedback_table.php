<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_giocatore_votante');
            $table->unsignedBigInteger('id_partita');
            $table->unsignedBigInteger('id_giocatore_votato');
            $table->string('commento')->nullable();
            $table->enum('voto', ['1','2','3','4','5']);
            $table->timestamps();

            $table->foreign('id_giocatore_votante')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_giocatore_votato')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_partita')->references('id')->on('match')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
