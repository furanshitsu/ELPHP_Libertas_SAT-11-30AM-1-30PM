<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class UserController extends Controller
{


    public function me()
    {
        $user = User::with('posts.comments.user')->find(auth()->id());
        $personal_info = [
            "username" => $user->username,
            "email" => $user->email,
            "created_at" => $user->created_at
        ];
        $posts = $user->posts->map(function ($post) {
            return [
                "post_id" => $post->id,
                'post_id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'created_at' => $post->created_at,
                'comments' => $post->comments->map(function ($comment) {
                    return [
                        "comment_id" => $comment->id,
                        'commentor' => $comment->user->username,
                        'content' => $comment->content,
                    ];
                }),
            ];
        });
        return response()->json(
            [
                "profile" => $personal_info,
                "My Post" => $posts
            ]
        );
    }



    public function update(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'email'    => 'required'
        ]);

        $user = User::findOrFail(auth()->id());

        $user->update([
            'username' => $request->input('username'),
            'email'    => $request->input('email'),
        ]);

        return response()->json([
            'message' => 'Account updated successfully',
            'user'    => $user
        ]);
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        $user = auth()->user();

        if (!$user || !Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect']);
        }

        $user->fill([
            'password' => Hash::make($request->new_password),
        ]);
        $user->save();


        return response()->json(['message' => 'Password updated successfully']);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        $user = auth()->user();
        if (!$user || !Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password is incorrect']);
        }

        $user = User::findorfail(auth()->id());

        $user->delete();
        $user->tokens()->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
