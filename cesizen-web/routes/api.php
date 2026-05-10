<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Article;
use App\Models\Emotion;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserEmotionController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/articles', function () {
    return Article::latest()->get()->map(function ($article) {
        $article->image_url = $article->image_url ? asset('storage/' . $article->image_url) : null;
        return $article;
    });
});

Route::get('/emotions-preview', function () {
    return Emotion::whereNull('parent_id')->get();
});


// --- ROUTES PROTÉGÉES (Utilisateurs connectés) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/emotions', function () {
        return Emotion::with('enfants')->whereNull('parent_id')->get();
    });

    Route::post('/user-emotions', [UserEmotionController::class, 'store']);
    Route::get('/user-emotions/stats', [UserEmotionController::class, 'stats']);
    Route::get('/user-emotions/history', [UserEmotionController::class, 'index']);
    Route::delete('/user-emotions/{id}', [UserEmotionController::class, 'destroy']);
});