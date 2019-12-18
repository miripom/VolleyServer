<?php

namespace App\Http\Controllers;

use App\Match;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class matchesController extends Controller
{


    function match_type(){

      $type=  DB::table('match_type')
            ->select('nome_tipologia')
            ->get();

            return $type->toArray();
    }

    function listmatches($idU) {

        $matches= DB::table('match')
           ->select('titolo', 'descrizione', 'luogo', 'tipo', 'organizzatore', 'match.id', 'data_ora')
            ->join('partecipation','match.organizzatore', '=','partecipation.id_giocatore')
            ->whereNotIn('match.id', function($query) use ($idU){
                $query->select('id_partita')
                    ->from('partecipation')
                    ->where('id_giocatore', '=', $idU);
            })
            ->orderByDesc('id')
            ->distinct()
            ->get();

        $result = $matches->map(function ($item, $key) {
            $item->organizzatore = DB::table('users')
                ->where('id', '=', $item->organizzatore)
                ->get();
            return $item;
        });



        return $matches->toJson();
    }

    function matchDetails($id, $idG)
    {

        $details = DB::table('match')
            ->select('titolo', 'descrizione', 'luogo', 'tipo', 'organizzatore', 'id', 'numero_giocatori')
            ->where('id', '=', $id)
            ->get();

        $result = DB::table('partecipation')
            ->where('id_partita', '=', $id)
            ->where('id_giocatore', '=', $idG)
            ->count('id');

        return response()->json([
            'details' =>$details,
            'result' => $result
        ], 201);

    }

        public function addMatch(Request $request)
        {
            $request->validate([
                'titolo' => 'required|string',
                'luogo' => 'required|string',
                'giocatori_richiesti' => 'required|string',
                'descrizione' => 'required|string',
                'data_ora' => 'required|date',
               // 'tipologia' => 'required|string',
                'organizzatore' => 'integer'
            ]);

            $match = new Match;
            $match->titolo = $request->titolo;
            $match->luogo = $request->luogo;
            $match->numero_giocatori = $request->giocatori_richiesti;
            $match->descrizione = $request->descrizione;
            $match->data_ora = $request->data_ora;
           // $match->tipo = $request->tipologia;
            $match->id_organizzatore = $request->organizzatore;
            $match->save();

            /*DB::table('users')
                ->where('id', '=', $id)
                ->increment('total_matches');*/

            return response()->json([
                'message' => 'Successfully created!'
            ], 201);
        }


    public function partecipazione() {
        $partecipa= DB::table('match')
            ->select('id')
            ->orderByDesc('created_at')
            ->first();


        $utente= DB::table('match')
            ->select('id_organizzatore')
            ->orderByDesc('created_at')
            ->first();

        DB::table('partecipation')
            ->insert([
                'id_partita'=> $partecipa->id,
                'id_giocatore' => $utente->organizzatore,
            ]);

        return response()->json([
            'message' => 'Successfully created!'
        ], 201);


}
    public function my_Matches($idUT)  {
        $partitemie= DB::table('match')
            ->select('titolo', 'descrizione', 'luogo', 'tipo', 'organizzatore', 'match.id', 'data_ora')
            //->join('users', 'match.organizzatore','=','users.id')
            ->join('partecipation', 'match.id', '=', 'partecipation.id_partita')
            ->where('partecipation.id_giocatore', '=', $idUT)
            ->where('match.data_ora','>=', Carbon::now())
            ->orderByDesc('match.id')
            ->distinct()
            ->get();

        $result = $partitemie->map(function ($item, $key) {
            $item->organizzatore = DB::table('users')
                ->where('id', '=', $item->organizzatore)
                ->get();
            return $item;
        });

        return $partitemie->toJson();

    }

    public function partecipa($idG,$idP) {

        DB::table('partecipation')
            ->insert([
                'id_giocatore' => $idG,
                'id_partita'=> $idP,
            ]);

        DB::table('match')->decrement('numero_giocatori');
        DB::table('users')
            ->where('id',  '=', $idG)
            ->increment('total_matches');

        return response()->json([
            'message' => 'Wow!'
        ], 201);

    }
public function partite_terminate($idUser) {

    $terminate = DB::table('match')
        ->select('titolo', 'descrizione', 'luogo', 'tipo', 'organizzatore', 'match.id', 'data_ora')
        //->join('users', 'match.organizzatore','=','users.id')
        ->join('partecipation', 'match.id', '=', 'partecipation.id_partita')
        ->where('partecipation.id_giocatore', '=', $idUser)
        ->where('match.data_ora','<', Carbon::now())
        ->orderByDesc('match.id')
        ->distinct()
        ->get();

    $result = $terminate->map(function ($item, $key) {
        $item->organizzatore = DB::table('users')
            ->where('id', '=', $item->organizzatore)
            ->get();
        return $item;
    });

    return $terminate->toJson();

}
}
