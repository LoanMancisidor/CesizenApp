<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ArticleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = new ArticleRepository();
        // Vérification d'un rôle existant pour éviter les erreurs de création d'utilisateur dans les factories
        if (\App\Models\Role::count() === 0) {
            \App\Models\Role::create(['id' => 1, 'libelle' => 'Administrateur']);
        }
    }

    /** @test */
    public function it_can_get_all_articles()
    {
        $user = User::factory()->create();
        Article::factory()->count(3)->create(['user_id' => $user->id]);

        $articles = $this->articleRepository->getAll();

        $this->assertCount(3, $articles);
    }

    /** @test */
    public function it_can_find_an_article_by_id()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $foundArticle = $this->articleRepository->findById($article->id);

        $this->assertInstanceOf(Article::class, $foundArticle);
        $this->assertEquals($article->titre, $foundArticle->titre);
    }

    /** @test */
    public function it_can_create_an_article()
    {
        $user = User::factory()->create();
        $data = [
            'titre' => 'Nouvel Article',
            'contenu' => 'Contenu de test',
            'user_id' => $user->id,
            'image_url' => 'articles/test.jpg'
        ];

        $article = $this->articleRepository->create($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertDatabaseHas('articles', ['titre' => 'Nouvel Article']);
    }

    /** @test */
    public function it_can_update_an_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $updateData = ['titre' => 'Titre Modifié'];

        $result = $this->articleRepository->update($article, $updateData);

        $this->assertTrue($result);
        $this->assertEquals('Titre Modifié', $article->fresh()->titre);
    }

    /** @test */
    public function it_deletes_the_file_and_the_article()
    {
        // 1. Simuler le stockage public
        Storage::fake('public');

        $user = User::factory()->create();

        // 2. Créer un faux fichier
        $filename = 'articles/photo.jpg';
        Storage::disk('public')->put($filename, 'fake content');

        $article = Article::factory()->create([
            'user_id' => $user->id,
            'image_url' => $filename
        ]);

        // 3. Action : Supprimer via le repository
        $result = $this->articleRepository->delete($article);

        // 4. Assertions
        $this->assertTrue($result);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);

        // Vérifie que le fichier a bien été supprimé du disque (Ligne 30-32 de ton repo)
        Storage::disk('public')->assertMissing($filename);
    }

    /** @test */
    public function it_deletes_article_even_without_image()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'image_url' => null
        ]);

        $result = $this->articleRepository->delete($article);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
