<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Article;
use App\Models\Emotion;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- ROUTES PUBLIQUES (Visiteurs) ---
// Accessible par Flutter sans être connecté
Route::get('/articles', function () {
    return Article::latest()->get()->map(function ($article) {
        // On transforme le chemin relatif en URL absolue pour Flutter
        $article->image_url = $article->image_url
            ? asset('storage/' . $article->image_url)
            : null;
        return $article;
    });
});

// Récupérer les émotions de base pour que le visiteur voit ce qui est possible
Route::get('/emotions-preview', function () {
    return Emotion::whereNull('parent_id')->get();
});


// --- ROUTES PROTÉGÉES (Utilisateurs connectés) ---
Route::middleware('auth:sanctum')->group(function () {

    // Récupérer les infos de l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Ici tu ajouteras plus tard les routes pour :
    // - Enregistrer une émotion dans le tracker
    // - Voir son historique personnel
});

// --- ROUTE D'AUTHENTIFICATION (Pour Flutter) ---
// On crée une route simple pour que Flutter puisse envoyer email/password et recevoir un Token
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
