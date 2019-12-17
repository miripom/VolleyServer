<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            //'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response(
                ['nome' => $user->nome,
                'cognome' => $user->cognome,
                'id' => $user->id,


            ], 200)->withHeaders([ 'Access-Control-Expose-Headers' => 'token','token' => $tokenResult->accessToken]);
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
}
