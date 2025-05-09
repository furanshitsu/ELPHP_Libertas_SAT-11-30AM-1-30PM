<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ViewUserController extends Controller
{
    //
    public function all()
    {
        $users = User::all();

        $all = $users->map(function ($user) {
            return [
                "username" => $user->username,
                "created_at" => $user->created_at
            ];
        });

        return response()->json(
            [
                "registered users" => $all
            ]
        );
    }

    public function user($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found']);
        }

        $user = [
            "username" => $user->username,
            "created_at" => $user->created_at
        ];


        return response()->json(
            ["registered user" => $user]
        );
    }
}
