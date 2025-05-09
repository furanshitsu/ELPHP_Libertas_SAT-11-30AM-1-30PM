<?php


namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostRequest;

use App\Models\Post;
use App\Models\User;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title'   => ['required'],
            'content' => ['required'],
        ]);


        $post = new Post();
        $post->fill([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
        $post->save();

        $post = [
            'title' => $post->title,
            'content' => $post->content
        ];

        return response()->json([
            'message' => 'Post created successfully',
            'post'    => $post
        ]);
    }


    public function update(UpdatePostRequest $request, $id)
    {
        $data = $request->validated();

        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found']);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $post->update($request->only(['title', 'content']));

        $post = [
            "post_id" => $post->id,
            "title" => $post->title,
            "content" => $post->content,
            "created_at" => $post->created_at,
            "updated_at" => $post->updated_at
        ];

        return response()->json([
            'message' => 'Post updated successfully',
            'updated' => $post
        ]);
    }


    public function destroy($id)
    {

        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'post  not found']);
        }
        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }


    public function showPostByUsername($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json('Author not found');
        }
        $user->load('posts.comments');

        $posts = $user->posts->map(function ($post) {
            return [
                "post_id" => $post->id,
                "title" => $post->title,
                "content" => $post->content,
                "created_at" => $post->created_at,
                "comments" => $post->comments->map(function ($comment) {
                    return [
                        "comment_id" => $comment->id,
                        "commentor" => $comment->user->username,
                        "content" => $comment->content

                    ];
                })
            ];
        });
        return response()->json([
            "Author" =>  $user->username,
            "Posts" => $posts
        ]);
    }
    public function showFullPost()
    {
        $post = Post::all();

        $post->load('user.comments');



        $posts = $post->map(function ($post) {
            return [
                "post_id" => $post->id,
                "title" => $post->title,
                "content" => $post->content,
                "author" => $post->user->username,
                "created_at" => $post->created_at,
                "comments" => $post->comments->map(function ($comment) {
                    return [
                        "comment_id" => $comment->id,
                        "commentor" => $comment->user->username,
                        "comment" => $comment->content
                    ];
                })
            ];
        });

        return response()->json(["Feed" => $posts]);
    }

    public function showRecentPosts()
    {
        $post = Post::latest()->get();

        if (!$post->isEmpty()) {
            $post->load(['user.comments.user']);
        }
        $posts = $post->map(function ($post) {
            return [
                "post_id" => $post->id,
                "title" => $post->title,
                "content" => $post->content,
                "author" => $post->user->username,
                "created_at" => $post->created_at,
                "comments" => $post->comments->map(function ($comment) {
                    return [
                        "comment_id" => $comment->id,
                        "commentor" => $comment->user->username,
                        "comment" => $comment->content
                    ];
                })
            ];
        });

        return response()->json(["recent feed" => $posts]);
    }

    public function showSinglePost($id)
    {
        $post = Post::with('user.comments')->where('id', $id)->first();

        if (!$post) {
            return response()->json(["message" => "post not found"]);
        }

        $posts = [
            "post_id" => $post->id,
            "title" => $post->title,
            "content" => $post->content,
            "author" => $post->user->username,
            "created_at" => $post->created_at->toDateTimeString(),
            "comments" => $post->comments->map(function ($comment) {
                return [
                    "comment_id" => $comment->id,
                    "commentor" => $comment->user->username,
                    "comment" => $comment->content
                ];
            })
        ];

        return response()->json($posts);
    }


    public function showPostsByKeyword($title)
    {
        $post = Post::with(['user.comments.user'])
            ->where('title', 'LIKE', "%{$title}%")->get();

        $posts = $post->map(function ($post) {
            return [
                "post_id" => $post->id,
                "title" => $post->title,
                "content" => $post->content,
                "author" => $post->user->username,
                "created_at" => $post->created_at->toDateTimeString(),
                "comments" => $post->comments->map(function ($comment) {
                    return [
                        "comment_id" => $comment->id,
                        "commentor" => $comment->user->username,
                        "comment" => $comment->content

                    ];
                })
            ];
        });


        return response()->json(["Search results" => $posts]);
    }
}
