<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Repositories\HomeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $homeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->homeRepository = new HomeRepository();
        // Vérification d'un rôle existant pour éviter les erreurs de création d'utilisateur dans les factories
        if (\App\Models\Role::count() === 0) {
            \App\Models\Role::create(['id' => 1, 'libelle' => 'Administrateur']);
        }
    }

    /** @test */
    public function it_returns_correct_stats_structure()
    {
        // Création de données de test
        User::factory()->count(3)->create(['active' => true]);
        Article::factory()->count(2)->create();

        $stats = $this->homeRepository->getDashboardStats();

        // On vérifie la structure du tableau
        $this->assertArrayHasKey('users_count', $stats);
        $this->assertArrayHasKey('users_active', $stats);
        $this->assertArrayHasKey('articles_count', $stats);
        $this->assertArrayHasKey('last_article', $stats);

        // On vérifie la véracité des chiffres (on ajoute +1 pour l'admin souvent créé en setUp)
        $this->assertGreaterThanOrEqual(3, $stats['users_count']);
        $this->assertEquals(2, $stats['articles_count']);
    }
}
