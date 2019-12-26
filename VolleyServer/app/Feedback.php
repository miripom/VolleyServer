<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'id_giocatore_votante',
        'id_giocatore_votato',
        'commento',
        'voto',
    ];
}
