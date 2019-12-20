<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $token = Str::random(32);
        DB::table('users')->where('email', $request->input('email'))->update(['token' => "$token"]);

        $ruolo= DB::table('role_type')
            ->where('id', '=', $user->id_ruolo)
            ->get();

        return response(
                ['nome' => $user->nome,
                'cognome' => $user->cognome,
                'id' => $user->id,
                'partite_totali' => $user->partite_totali,
                'ruolo' => $ruolo[0],
                'descrizione' => $user->descrizione


            ], 200)->withHeaders([ 'token' => $token]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'cognome' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'telefono' => 'required|string',
            'nome_ruolo' => 'required|string'
        ]);

        $nome_ruolo = $request->input('nome_ruolo');

        $ruolo = DB::table('role_type')
            ->select('id')
            ->where('nome_ruolo', '=', $nome_ruolo)
            ->value('id');


        $user = new User;
        $user->nome = $request->nome;
        $user->cognome = $request->cognome;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->telefono = $request->telefono;
        $user->id_ruolo = $ruolo;
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

     public function role_type() {
        $role= DB::table('role_type')
            ->select('id', 'nome_ruolo')
            ->get();

        return $role->toJson();
     }
}
