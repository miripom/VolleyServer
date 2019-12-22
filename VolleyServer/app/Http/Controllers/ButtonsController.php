<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ButtonsController extends Controller
{
    public function Cancella($id) {


        DB::table('users')
            ->select('partite_totali')
            ->whereIn('id', function($query) use ($id){
                $query->select('id_giocatore')
                    ->from('partecipation')
                    ->where('id_partita', '=', $id);
            })
            ->decrement('partite_totali');

        DB::table('match')
            ->where('id','=', $id)
            ->delete();

        return response()->json([
            'message' => 'Partita cancellata correttamente'
        ]);
    }

    public function removePartecipation ($id, Request $request) {

        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');


        DB::table('partecipation')
            ->where('id_partita', '=', $id)
            ->where('id_giocatore', '=', $idUtente)
            ->delete();

        DB::table('users')
            ->where('id', '=', $idUtente)
            ->decrement('partite_totali');

        DB::table('match')
            ->where('id', '=', $id)
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
