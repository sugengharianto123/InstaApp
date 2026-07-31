<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StoryController;
use Illuminate\Support\Facades\Route;

// Route Publik
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route Privat (Wajib Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // --- ROUTE POSTS ---
    Route::get('/posts', [PostController::class, 'index']);       // Ambil semua post
    Route::post('/posts', [PostController::class, 'store']);      // Buat post baru
    Route::delete('/posts/{post}', [PostController::class, 'destroy']); // Hapus post

    // --- ROUTE LIKES ---
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    // Route Komentar
    Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike']); // <-- TAMBAHKAN INI



    // User
    Route::get('/users/suggested', [UserController::class, 'suggested']);
    Route::get('/users/me', [UserController::class, 'profile']);
    Route::get('/users/me/posts', [UserController::class, 'myPosts']);
    Route::put('/users/me', [UserController::class, 'update']);

    // Stories
    Route::get('/stories', [StoryController::class, 'index']);
    Route::post('/stories', [StoryController::class, 'store']);
    Route::delete('/stories/{story}', [StoryController::class, 'destroy']);
    Route::post('/stories/cleanup', [StoryController::class, 'cleanupExpired']);
});



