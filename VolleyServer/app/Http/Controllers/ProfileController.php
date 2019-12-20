<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function updateDescr(Request $request){

        $access_token = $request->header('token');

        $idUtente = DB::table('users')
            ->select('id')
            ->where('users.token', '=', $access_token)
            ->value('id');


        DB::table('users')
            ->where('id', '=', $idUtente)
            ->update(['users.descrizione'=> $request->input('descrizione')

            ]);

        return response()->json([
            'message' => 'Successfully updated!',
            'data' =>  $request->input('descrizione')
        ], 201);

    }
}
