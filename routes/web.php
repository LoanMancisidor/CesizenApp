<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\EmotionController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. La racine du site : on la protège par "auth" pour que personne ne voit le site sans être connecté.
Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('articles', ArticleController::class);
    Route::resource('emotions', EmotionController::class);
    Route::get('emotions/parent/{emotion}', [EmotionController::class, 'showSubEmotions'])->name('emotions.parent');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);    
    Route::patch('admin/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 4. Les routes d'authentification (Login, Register, Logout)
require __DIR__.'/auth.php';