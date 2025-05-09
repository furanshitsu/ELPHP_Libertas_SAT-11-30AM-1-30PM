<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;


use Illuminate\Support\Facades\Hash;

class SessionController extends Controller

{
    //

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials']);
        }

        $user->tokens()->delete();

        $token = $user->createToken('postman')->plainTextToken;

        return response()->json([
            'message' => 'Logged in',
            'token' => $token,
            'user' => $user
        ]);
    }


    public function status(Request $request)
    {
        return response()->json([
            'message' => 'You are authenticated',
            'user' => $request->user()
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
