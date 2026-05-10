<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Journal;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        if (!class_exists(Journal::class)) {
            return;
        }

        Journal::truncate();

        Journal::insert([
            [
                'user_id' => 1,
                'emotion_id' => 1,
                'contenu' => 'Je me sens bien aujourd’hui.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'emotion_id' => 2,
                'contenu' => 'J’ai eu une journée stressante.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
