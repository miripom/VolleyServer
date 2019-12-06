<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartecipationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partecipation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_partita');
            $table->unsignedBigInteger('id_giocatore');
            $table->timestamps();

            $table->foreign('id_partita')->references('id')->on('match')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_giocatore')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partecipation');
    }
}
