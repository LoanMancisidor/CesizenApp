<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::truncate();

        Article::insert([
            [
                'titre' => 'Bienvenue sur Cesizen',
                'contenu' => 'Premier article de démonstration.',
                'image_url' => null,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Gestion du stress',
                'contenu' => 'Quelques conseils pour mieux gérer son stress.',
                'image_url' => null,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}