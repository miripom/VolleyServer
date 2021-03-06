<?php

namespace App\Http\Controllers;

use App\Match;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class matchesController extends Controller
{


    public function match_type()
    {

        $type = DB::table('match_type')
            ->select('id', 'nome_tipologia')
            ->get();

        return $type->toJson();
    }

    function listmatches()
    {

        $matches = DB::table('match')
            ->select('titolo', 'descrizione', 'luogo', 'id_tipologia_partita', 'id_organizzatore', 'match.id', 'data_ora')
            ->join('partecipation', 'match.id_organizzatore', '=', 'partecipation.id_giocatore')
            ->where('match.data_ora', '>=', Carbon::now())
            ->orderByDesc('id')
            ->distinct()
            ->get();

       $matches->map(function ($items, $key) {
            $items->id_organizzatore = DB::table('users')
                ->where('id', '=', $items->id_organizzatore)
                ->first();

            return $items;
        });

       $matches->map(function ($item, $key) {
            $item->id_tipologia_partita = DB::table('role_type')
                ->where('id', '=', $item->id_tipologia_partita)
                ->first();

            return $item;
        });


        return $matches->toJson();
    }

    function matchDetails($id)
    {

        $details = DB::table('match')
            ->select('titolo', 'descrizione', 'luogo', 'id_tipologia_partita', 'id_organizzatore', 'id', 'numero_giocatori', 'data_ora')
            ->where('id', '=', $id)
            ->distinct()
            ->get();

        $details->map(function ($item, $key) {
            $item->id_organizzatore = DB::table('users')
                ->select('id')
                ->where('id', '=', $item->id_organizzatore)
                ->first();
            return $item;
        });


    return $details->toJson();

    }

    public function checkP(Request $request, $id){
        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $result = DB::table('partecipation')
            ->where('id_partita', '=', $id)
            ->where('id_giocatore', '=', $idUtente)
            ->count('id');

        return $result;
    }

    public function addMatch(Request $request)
    {
        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $request->validate([
            'titolo' => 'required|string',
            'luogo' => 'required|string',
            'giocatori_richiesti' => 'required',
            'descrizione' => 'required|string',
            'data_ora' => 'required|date',
            'tipologia' => 'required|string',
            'organizzatore' => 'integer'
        ]);

        $id_tipologia = DB::table('match_type')
            ->select('id')
            ->where('nome_tipologia', '=', $request->tipologia)
            ->value('id');

        $match = new Match;
        $match->titolo = $request->titolo;
        $match->luogo = $request->luogo;
        $match->numero_giocatori = $request->giocatori_richiesti;
        $match->descrizione = $request->descrizione;
        $match->data_ora = $request->data_ora;
        $match->id_tipologia_partita = $id_tipologia;
        $match->id_organizzatore = $request->organizzatore;
        $match->save();

        DB::table('users')
            ->where('id', '=', $idUtente)
            ->increment('partite_totali');

        return response()->json([
            'message' => 'Successfully created!'
        ], 201);
    }


    public function partecipazioneOrg()
    {

        $partecipa = DB::table('match')
            ->select('id')
            ->orderByDesc('created_at')
            ->first();


        $utente = DB::table('match')
            ->select('id_organizzatore')
            ->orderByDesc('created_at')
            ->first();


        DB::table('partecipation')
            ->insert([
                'id_partita' => $partecipa->id,
                'id_giocatore' => $utente->id_organizzatore,
            ]);

        return response()->json([
            'message' => 'Successfully created!'
        ], 201);


    }

    public function my_Matches(Request $request)
    {
        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $partitemie = DB::table('match')
            ->select('titolo', 'descrizione', 'luogo', 'id_tipologia_partita', 'id_organizzatore', 'match.id', 'data_ora')
            ->join('partecipation', 'match.id', '=', 'partecipation.id_partita')
            ->where('partecipation.id_giocatore', '=', $idUtente)
            ->where('match.data_ora', '>=', Carbon::now())
            ->orderByDesc('match.id')
            ->distinct()
            ->get();

        $partitemie->map(function ($item, $key) {
            $item->id_organizzatore = DB::table('users')
                ->where('id', '=', $item->id_organizzatore)
                ->first();
            return $item;
        });

        $partitemie->map(function ($item, $key) {
            $item->id_tipologia_partita = DB::table('role_type')
                ->where('id', '=', $item->id_tipologia_partita)
                ->first();

            return $item;
        });

        return $partitemie->toJson();
    }

    public function partecipa($id, Request $request)
    {
        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        DB::table('partecipation')
            ->insert([
                'id_giocatore' => $idUtente,
                'id_partita' => $id,
            ]);

        DB::table('match')->decrement('numero_giocatori');
        DB::table('users')
            ->where('id', '=', $idUtente)
            ->increment('partite_totali');

        return response()->json([
            'message' => 'Wow!'
        ], 201);

    }

    public function partite_terminate(Request $request)
    {
        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $terminate = DB::table('match')
            ->select('titolo', 'descrizione', 'luogo', 'id_tipologia_partita', 'id_organizzatore', 'match.id', 'data_ora')
            ->join('partecipation', 'match.id', '=', 'partecipation.id_partita')
            ->where('partecipation.id_giocatore', '=', $idUtente)
            ->where('match.data_ora', '<', Carbon::now())
            ->orderByDesc('match.id')
            ->distinct()
            ->get();

        $terminate->map(function ($item, $key) {
            $item->id_organizzatore = DB::table('users')
                ->where('id', '=', $item->id_organizzatore)
                ->first();
            return $item;
        });

        $terminate->map(function ($item, $key) {
            $item->id_tipologia_partita = DB::table('role_type')
                ->where('id', '=', $item->id_tipologia_partita)
                ->first();

            return $item;
        });

        return $terminate->toJson();

    }

    public function allplayers($id) {
        $players= DB::table('partecipation')
            ->join('users', 'partecipation.id_giocatore', '=', 'users.id')
            ->join('role_type', 'users.id_ruolo', '=', 'role_type.id')
            ->select('users.nome', 'users.cognome', 'users.id_ruolo')
            ->where('partecipation.id_partita', '=', $id)
            ->distinct()
            ->get();

        $players->map(function ($items, $key) {
            $items->id_ruolo = DB::table('role_type')
                ->select('nome_ruolo')
                ->where('id', '=', $items->id_ruolo)
                ->first();

            return $items;
        });

        return $players->toJson();
}



    public function feedbackPlayers(Request $request, $idpartita) {

        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');

        $players= DB::table('partecipation')
            ->join('users', 'partecipation.id_giocatore', '=', 'users.id')
            ->join('role_type', 'users.id_ruolo', '=', 'role_type.id')
            ->select('users.nome', 'users.cognome', 'users.id_ruolo', 'users.id')
            ->where('partecipation.id_partita', '=', $idpartita)
            ->where('partecipation.id_giocatore', '<>', $idUtente)
            ->distinct()
            ->get();

        $players->map(function ($items, $key) {
            $items->id_ruolo = DB::table('role_type')
                ->select('nome_ruolo')
                ->where('id', '=', $items->id_ruolo)
                ->first();

            return $items;
        });

        return $players->toJson();
    }


}
