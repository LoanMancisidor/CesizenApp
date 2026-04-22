<?php

namespace App\Repositories;

use App\Models\{User, Article, Emotion};

class HomeRepository
{
    public function getDashboardStats()
    {
        return [
            'users_count' => User::count(),
            'users_active' => User::where('active', true)->count(),
            'articles_count' => Article::count(),
            'emotions_count' => Emotion::count(),
            'last_article' => Article::latest()->first(),
            'recent_users' => User::latest()->take(5)->get(),
        ];
    }
}
