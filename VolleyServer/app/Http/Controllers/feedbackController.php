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

        $request->validate([
            'idGiocatore' => 'required|integer',
            'voto' => 'required|integer',
        ]);

        $id_giocatore_votante = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $id_giocatore_votato = DB::table('users')
            ->select('id')
            ->where('id', '=', $request->idGiocatore)
            ->value('id');



        $feedback = new Feedback();
        $feedback->id_giocatore_votante = $id_giocatore_votante;
        $feedback->id_giocatore_votato = $id_giocatore_votato;
        $feedback->voto = $request->voto;
        $feedback->save();


        return response()->json([
            'message' => 'Successfully created!'
        ], 201);
    }
}
