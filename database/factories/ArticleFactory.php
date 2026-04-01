<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(),
            'contenu' => $this->faker->paragraphs(3, true),
            'image_url' => 'articles/fake_image.jpg',
            'user_id' => \App\Models\User::factory(), // Crée un user si non fourni
        ];
    }
}
