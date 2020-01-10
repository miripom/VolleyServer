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

        $id_giocatore_votato = DB::table('users')
            ->select('id')
            ->where('id', '=', $request->input('id_giocatore_votato'))
            ->value('id');




        DB::table('feedback')
            ->insert([
                'id_giocatore_votato' => $request->input('id_giocatore_votato'),
                'id_giocatore_votante' => $id_giocatore_votante,
                'id_partita' => $request->input('id_partita'),
                'commento' =>$request->input('commento'),
                'voto' =>$request->input('voto')
            ]);




        /*$feedback = new Feedback();
        $feedback->id_giocatore_votante = $id_giocatore_votante;
        $feedback->id_giocatore_votato = $id_giocatore_votato;
        $feedback->voto = $request->input('voto');
        $feedback->commento = $request->input('commento');
        $feedback->id_partita = $request->input('id_partita');
        $feedback->save();*/

        return response()->json([
            'message' => 'Successfully created!'
        ], 201);
    }
}
