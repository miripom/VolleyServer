<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class feedbackController extends Controller
{
    public function addVoto(Request $request)
    {
        $access_token = $request->header('token');

        $id_giocatore_votante = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');


        DB::table('feedback')
            ->insert([
                'id_giocatore_votato' => $request->input('id_giocatore_votato'),
                'id_giocatore_votante' => $id_giocatore_votante,
                'id_partita' => $request->input('id_partita'),
                'commento' =>$request->input('commento'),
                'voto' =>$request->input('voto')
            ]);


        return response()->json([
            'message' => 'Successfully created!'
        ], 201);
    }

    public function checkFeedback(Request $request){
        $access_token = $request->header('token');

        $id_giocatore_votante = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $feedback = DB::table('feedback')
            ->select('commento', 'voto')
            ->where('id_giocatore_votante', '=', $id_giocatore_votante)
            ->where('id_giocatore_votato', '=', $request->input('id_giocatore'))
            ->where('id_partita', '=', $request->input('id_partita'))
            ->get();

        $check = DB::table('feedback')
            ->where('id_partita', '=', $request->input('id_partita'))
            ->where('id_giocatore_votato', '=', $request->input('id_giocatore'))
            ->where('id_giocatore_votante', '=', $id_giocatore_votante)
            ->count('id');

        return response()->json([
            'feedback' => $feedback,
            'check' => $check
        ]);

    }

    public function getMedia(Request $request){
        $access_token = $request->header('token');

        $id= DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $results =DB::table('feedback')
                ->where('id_giocatore_votato', '=', $id)
                ->avg('voto');


        return $results;
    }

    public function getCommenti(Request $request){

        $access_token = $request->header('token');

        $id = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $commenti = DB::table('feedback')
            ->join('users', 'feedback.id_giocatore_votato', '=', 'users.id')
            ->select('feedback.commento', 'feedback.voto', 'feedback.id_giocatore_votante AS utente_id', 'feedback.id')
            ->where('feedback.id_giocatore_votato','=', $id)
            ->orderByDesc('feedback.id')
            ->distinct()
            ->get();

        $commenti->map(function ($item, $key) {
            $item->id_giocatore_votante= DB::table('users')
                ->where('id', '=', $item->utente_id)
                ->select('users.nome', 'users.cognome')
                ->distinct()
                ->first();

            return $item;
        });

        return $commenti->toJson();
    }


}
