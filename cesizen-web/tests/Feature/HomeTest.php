<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Emotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste le chargement du dashboard avec des données
     */
    public function test_dashboard_affiche_les_statistiques_et_les_donnees()
    {
        $admin = $this->loginAdmin();

        User::factory()->create(['name' => 'Jean Actif', 'active' => true]);
        User::factory()->create(['name' => 'Luc Inactif', 'active' => false]);

        Article::factory()->count(2)->create();
        Article::factory()->create(['titre' => 'Mon dernier article']);

        Emotion::factory()->count(10)->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    /**
     * Teste le cas spécifique où il n'y a aucun article
     */
    public function test_dashboard_gere_le_cas_sans_article()
    {
        $this->loginAdmin();

        Article::query()->delete();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Aucun article pour le moment.');
    }
}
