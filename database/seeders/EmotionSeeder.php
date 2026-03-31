<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Emotion::insert([
            ['nom' => 'Joie', 'niveau' => 1, 'image_icone' => 'joy.png'],
            ['nom' => 'Stress', 'niveau' => 3, 'image_icone' => 'stress.png'],
            ['nom' => 'Colère', 'niveau' => 4, 'image_icone' => 'angry.png'],
            ['nom' => 'Calme', 'niveau' => 1, 'image_icone' => 'calm.png'],
        ]);
    }
}
