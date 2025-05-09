<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class RegisteredUserController extends Controller
{

    public function store(Request $request)
    {

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);


        $user = new User();
        $user->fill([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => $request->password,
        ]);
        $user->save();

        return response()->json([
            'message' => 'Registered successfully please login',
            'username'    => $user->username,
            'email' => $user->email
        ]);
    }
}
