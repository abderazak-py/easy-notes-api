<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\FollowerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Profile routes
    Route::post('/profile/setup', [ProfileController::class, 'setup']);
    Route::put('/profile', [ProfileController::class, 'updateNameBio']);
    Route::get('/profile', [ProfileController::class, 'show']);

    Route::get('/notes', [NoteController::class, 'index']);
    Route::get('/notes/my', [NoteController::class, 'myNotes']);
    Route::get('/notes/liked', [LikeController::class, 'myLikes']);
    Route::post('/notes', [NoteController::class, 'store'])->middleware('profile.setup');
    Route::get('/notes/{note}', [NoteController::class, 'show']);
    Route::put('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);
    Route::get('/public-notes', [NoteController::class, 'publicIndex']);
    Route::get('/public-notes/{note}', [NoteController::class, 'publicShow']);
    Route::post('/notes/{note}/like', [LikeController::class, 'like']);
    Route::delete('/notes/{note}/like', [LikeController::class, 'unlike']);

    Route::get('/notes/{note}/comments', [CommentController::class, 'index']);
    Route::post('/notes/{note}/comments', [CommentController::class, 'store'])->middleware('profile.setup');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::apiResource('follow', FollowerController::class)->only(['index', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
