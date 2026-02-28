<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notes', [NoteController::class, 'index']);
    Route::get('/notes/my', [NoteController::class, 'myNotes']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::get('/notes/{note}', [NoteController::class, 'show']);
    Route::put('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);
    Route::get('/public-notes', [NoteController::class, 'publicIndex']);
    Route::get('/public-notes/{note}', [NoteController::class, 'publicShow']);

    Route::get('/notes/liked', [LikeController::class, 'myLikes']);
    Route::post('/notes/{note}/like', [LikeController::class, 'like']);
    Route::delete('/notes/{note}/like', [LikeController::class, 'unlike']);

    Route::get('/notes/{note}/comments', [CommentController::class, 'index']);
    Route::post('/notes/{note}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

require __DIR__.'/auth.php';
