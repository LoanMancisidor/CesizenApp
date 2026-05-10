<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(
            ['id' => 1],
            ['libelle' => 'Administrateur']
        );
    }

    private function login()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role_id' => 1,
            'active' => true
        ]);

        $this->actingAs($user);
        return $user;
    }

    public function test_la_liste_des_articles_s_affiche_avec_les_bonnes_informations()
    {
        $user = User::factory()->create(['role_id' => 1]);

        $article = Article::create([
            'titre' => 'Article de Test',
            'contenu' => 'Contenu de test',
            'user_id' => $user->id,
            'image_url' => 'articles/fake.jpg'
        ]);

        $user = $this->login();
        $response = $this->get(route('articles.index'));

        $response->assertStatus(200);
        $response->assertSee('Article de Test');
    }

    public function test_un_article_peut_etre_cree_avec_une_image()
    {
        Storage::fake('public');
        $user = $this->login();

        $image = UploadedFile::fake()->image('couverture.jpg');

        $response = $this->post(route('articles.store'), [
            'titre' => 'Mon nouvel article',
            'contenu' => 'L\'herbe est verte.',
            'image' => $image,
        ]);

        $response->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', [
            'titre' => 'Mon nouvel article',
            'user_id' => $user->id
        ]);

        $article = Article::where('titre', 'Mon nouvel article')->first();
        Storage::disk('public')->assertExists($article->image_url);
    }

    public function test_la_creation_echoue_si_le_titre_est_manquant()
    {
        $this->login();

        $response = $this->post(route('articles.store'), [
            'titre' => '',
            'contenu' => 'Blabla',
        ]);

        $response->assertSessionHasErrors('titre');
    }

    public function test_la_page_d_edition_affiche_les_donnees_actuelles()
    {
        $user = $this->login();
        $article = Article::create([
            'titre' => 'Modifier ce titre',
            'contenu' => 'Contenu original',
            'user_id' => $user->id
        ]);

        $response = $this->get(route('articles.edit', $article->id));

        $response->assertStatus(200);
        $response->assertSee('Modifier ce titre');
    }

    public function test_un_article_peut_etre_mis_a_jour_avec_une_nouvelle_image()
    {
        $user = $this->login();
        $article = Article::factory()->create(['user_id' => $user->id]);
        Storage::fake('public');

        $response = $this->put(route('articles.update', $article), [
            'titre' => 'Titre Modifié',
            'contenu' => 'Ceci est le contenu mis à jour',
            'image' => UploadedFile::fake()->image('new_cover.jpg')
        ]);

        $response->assertRedirect(route('articles.index'));
        $this->assertDatabaseHas('articles', ['titre' => 'Titre Modifié']);

        $article->refresh();
        Storage::disk('public')->assertExists($article->image_url);
    }

    public function test_la_suppression_fonctionne_via_une_requete_standard()
    {
        $user = $this->login();
        $article = Article::create([
            'titre' => 'A supprimer',
            'contenu' => 'Test',
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('articles.destroy', $article->id));

        $response->assertRedirect(route('articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_la_suppression_renvoie_du_json_si_demande()
    {
        $user = $this->login();
        $article = Article::create([
            'titre' => 'Suppression JS',
            'contenu' => 'Test',
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson(route('articles.destroy', $article->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Article supprimé']);
    }

    public function test_le_telechargement_d_un_fichier_non_image_est_rejete()
    {
        $this->login();
        $file = UploadedFile::fake()->create('document.pdf', 500);

        $response = $this->post(route('articles.store'), [
            'titre' => 'Titre',
            'contenu' => 'Contenu',
            'image' => $file,
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_la_page_de_creation_est_accessible()
    {
        $this->login();
        $response = $this->get(route('articles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('articles.create');
    }
}
