<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ViewUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


# should be out
Route::POST('/login', [SessionController::class, 'login']);
Route::POST('/register', [RegisteredUserController::class, 'store']);


# Authentication
Route::middleware('auth:sanctum')->controller(SessionController::class)->group(function () {
    Route::GET('/status', 'status');
    Route::POST('/logout', 'logout');
});

# view registered
Route::middleware('auth:sanctum')->controller(ViewUserController::class)->group(function () {
    Route::GET('/users', 'all'); # list of all registered user
    Route::GET('/users/{username}', 'user'); # view single user profile
});

# User (Authenticated)
Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {
    Route::GET('/me', 'me'); // Get authenticated user info plus all of its post and associated comments
    Route::PATCH('/me', 'update'); // Update user info
    Route::PATCH('/me/password', 'password'); // Update password
    Route::DELETE('/me', 'delete'); // Delete account
});

# Posts (Authenticated)
Route::middleware('auth:sanctum')->controller(PostController::class)->group(function () {
    Route::POST('/posts', 'store'); // Create post
    Route::PATCH('/posts/{id}', 'update'); // Update post
    Route::DELETE('/posts/{id}', 'destroy'); // Delete post
});

# Comments (Authenticated)
Route::middleware('auth:sanctum')->controller(CommentController::class)->group(function () {
    Route::POST('/posts/{post_id}/comments', 'store'); // Create comment under a specific post
    Route::PATCH('/comments/{id}', 'update'); // Update comment
    Route::DELETE('/comments/{id}', 'destroy'); // Delete comment
});

# posts (Public)
Route::controller(PostController::class)->group(function () {

    Route::GET('/posts', 'showFullPost'); // Show all posts
    Route::GET('/posts/recent', 'showRecentPosts'); // show recent
    Route::GET('/posts/title/{title}', 'showPostsByKeyword'); // Show posts with keyword 
    Route::GET('/posts/username/{username}', 'showPostByUsername'); // Show all posts specific to username  
    Route::GET('/posts/{id}', 'showSinglePost'); // Show single post by post id 
});


