<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function updateProfile($id, Request $request){


        DB::table('users')
            ->where('id', '=', $id)
            ->update(['users.description'=> $request->input('descr')

            ]);

        return response()->json([
            'message' => 'Successfully updated!',
            'data' =>  $request->input('descr')
        ], 201);

    }
}
