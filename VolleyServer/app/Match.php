<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $table = 'match';

    protected $fillable = [
        'titolo',
        'descrizione',
        'data_ora',
        'luogo',
        'numero_giocatori',
        'tipo',
        'organizzatore',
    ];
}
