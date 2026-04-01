<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => \App\Models\User::count(),
            'users_active' => \App\Models\User::where('active', true)->count(),
            'articles_count' => \App\Models\Article::count(),
            'emotions_count' => \App\Models\Emotion::count(),
            'last_article' => \App\Models\Article::latest()->first(),
            'recent_users' => \App\Models\User::latest()->take(5)->get(),
        ];

        return view('home', compact('stats'));
    }
}
