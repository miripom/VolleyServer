<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_tipologia_partita');
            $table->unsignedBigInteger('id_organizzatore');
            $table->string('titolo');
            $table->string('descrizione');
            $table->string('data_ora');
            $table->string('luogo');
            $table->integer('numero_giocatori');
            $table->timestamps();

            $table->foreign('id_organizzatore')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_tipologia_partita')->references('id')->on('match_type')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match');
    }
}
