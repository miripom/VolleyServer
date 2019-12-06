<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ButtonsController extends Controller
{
    public function Cancella(Request $request) {

        $idmatch = $request->input('idPartita');

        DB::table('users')
            ->select('total_matches')
            ->whereIn('id', function($query) use ($idmatch){
                $query->select('id_giocatore')
                    ->from('partecipation')
                    ->where('id_partita', '=', $idmatch);
            })
            ->decrement('total_matches');

        DB::table('match')
            ->where('id','=', $idmatch)
            ->delete();

        return response()->json([
            'message' => 'Partita cancellata correttamente'
        ]);
    }

    public function removePartecipation (Request $request) {
        $idMatch = $request->input('idP');
        $idUser = $request->input('idG');

        DB::table('partecipation')
            ->where('id_partita', '=', $idMatch)
            ->where('id_giocatore', '=', $idUser)
            ->delete();

        DB::table('users')
            ->where('id', '=', $idUser)
            ->decrement('total_matches');

        DB::table('match')
            ->where('id', '=', $idMatch)
            ->increment('numero_giocatori');

        return response()->json([
            'message' => 'Partecipazione cancellata correttamente'
        ]);

    }


    public function lasciaFeedback ($id, $idU) {

        $giocatori = DB::table('users')
            ->join('partecipation', 'users.id', '=', 'partecipation.id_giocatore')
            ->select('users.name', 'users.surname', 'users.id')
            ->where('partecipation.id_partita','=', $id)
            ->where('users.id', '<>', $idU)
            ->distinct()
            ->get();

        return $giocatori->toJson();
    }
}
