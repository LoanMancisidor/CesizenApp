<?php

namespace Database\Factories;

use App\Models\Emotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmotionFactory extends Factory
{
    protected $model = Emotion::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->word(),
            // Par défaut, on crée une émotion de niveau 1 sans parent
            'niveau' => 1,
            'parent_id' => null,
            // On génère un faux chemin d'image pour les tests de vue
            'image_icone' => 'emotions/' . $this->faker->image(null, 640, 480, 'emojis', false),
        ];
    }

    /**
     * État pour créer facilement une sous-émotion (Niveau 2)
     */
    public function enfant(int $parentId): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parentId,
            'niveau' => 2,
        ]);
    }
}
