<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Emotion;

class EmotionSeeder extends Seeder
{
    public function run(): void
    {
        Emotion::truncate();

        // Émotions principales
        Emotion::insert([
            [
                'id' => 1,
                'nom' => 'Joie',
                'niveau' => 1,
                'image_icone' => 'joy.png',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nom' => 'Stress',
                'niveau' => 1,
                'image_icone' => 'stress.png',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nom' => 'Colère',
                'niveau' => 1,
                'image_icone' => 'angry.png',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nom' => 'Calme',
                'niveau' => 1,
                'image_icone' => 'calm.png',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Émotions secondaires
        Emotion::insert([

            // Joie
            [
                'nom' => 'Excitation',
                'niveau' => 2,
                'image_icone' => 'excited.png',
                'parent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Fierté',
                'niveau' => 2,
                'image_icone' => 'proud.png',
                'parent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Stress
            [
                'nom' => 'Anxiété',
                'niveau' => 2,
                'image_icone' => 'anxiety.png',
                'parent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Pression',
                'niveau' => 2,
                'image_icone' => 'pressure.png',
                'parent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Colère
            [
                'nom' => 'Frustration',
                'niveau' => 2,
                'image_icone' => 'frustration.png',
                'parent_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Rage',
                'niveau' => 2,
                'image_icone' => 'rage.png',
                'parent_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Calme
            [
                'nom' => 'Sérénité',
                'niveau' => 2,
                'image_icone' => 'serenity.png',
                'parent_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Détente',
                'niveau' => 2,
                'image_icone' => 'relax.png',
                'parent_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}