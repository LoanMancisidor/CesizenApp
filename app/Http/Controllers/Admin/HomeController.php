<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'articles_count' => Article::count(),
            'users_count'    => User::count(),
            'last_article'   => Article::latest()->first(),
        ];

        return view('home', compact('stats'));
    }
}