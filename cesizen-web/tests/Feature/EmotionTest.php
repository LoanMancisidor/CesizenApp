<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Emotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_emotions_affichage()
    {
        $this->loginAdmin();
        Emotion::factory()->create(['nom' => 'Joie']);

        $response = $this->get(route('emotions.index'));

        $response->assertStatus(200);
        $response->assertSee('Joie');
    }

    public function test_creation_emotion_de_base()
    {
        $this->loginAdmin();
        Storage::fake('public');

        $response = $this->post(route('emotions.store'), [
            'nom' => 'Colère',
            'parent_id' => null,
            'image_icone' => UploadedFile::fake()->image('colere.png')
        ]);

        $response->assertRedirect(route('emotions.index'));
        $this->assertDatabaseHas('emotions', [
            'nom' => 'Colère',
            'niveau' => 1,
            'parent_id' => null
        ]);

        $emotion = Emotion::where('nom', 'Colère')->first();
        Storage::disk('public')->assertExists($emotion->image_icone);
    }

    public function test_creation_sous_emotion_calcule_niveau_2()
    {
        $this->loginAdmin();
        $parent = Emotion::factory()->create(['nom' => 'Peur']);

        $response = $this->post(route('emotions.store'), [
            'nom' => 'Anxiété',
            'parent_id' => $parent->id
        ]);

        $this->assertDatabaseHas('emotions', [
            'nom' => 'Anxiété',
            'parent_id' => $parent->id,
            'niveau' => 2
        ]);
    }

    public function test_mise_a_jour_emotion_et_suppression_ancienne_image()
    {
        $this->loginAdmin();
        Storage::fake('public');

        // On utilise la factory pour générer l'émotion avec une image initiale
        $emotion = Emotion::factory()->create([
            'nom' => 'Tristesse',
            'image_icone' => UploadedFile::fake()->image('old.png')->store('emotions', 'public')
        ]);
        $oldPath = $emotion->image_icone;

        $response = $this->put(route('emotions.update', $emotion), [
            'nom' => 'Grosse Tristesse',
            'image_icone' => UploadedFile::fake()->image('new.png')
        ]);

        $emotion->refresh();
        $this->assertEquals('Grosse Tristesse', $emotion->nom);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($emotion->image_icone);
    }

    public function test_suppression_emotion_supprime_aussi_les_enfants_et_les_fichiers()
    {
        $this->loginAdmin();
        Storage::fake('public');

        // Création parent + enfant via factory avec images
        $parent = Emotion::factory()->create([
            'image_icone' => UploadedFile::fake()->image('p.png')->store('emotions', 'public')
        ]);
        $enfant = Emotion::factory()->enfant($parent->id)->create([
            'image_icone' => UploadedFile::fake()->image('e.png')->store('emotions', 'public')
        ]);

        $imgParent = $parent->image_icone;
        $imgEnfant = $enfant->image_icone;

        $response = $this->delete(route('emotions.destroy', $parent));

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('emotions', ['id' => $parent->id]);
        $this->assertDatabaseMissing('emotions', ['id' => $enfant->id]);

        Storage::disk('public')->assertMissing($imgParent);
        Storage::disk('public')->assertMissing($imgEnfant);
    }

    public function test_affichage_sous_emotions_specifiques()
    {
        $this->loginAdmin();
        $parent = Emotion::factory()->create(['nom' => 'Joie']);
        $enfant = Emotion::factory()->enfant($parent->id)->create(['nom' => 'Extase']);

        $response = $this->get(route('emotions.parent', $parent));

        $response->assertStatus(200);
        $response->assertSee('Extase');
        $response->assertSee('Joie');
    }

    public function test_la_page_de_creation_emotion_est_accessible()
    {
        $this->loginAdmin();
        Emotion::factory()->create(['nom' => 'Joie']);

        $response = $this->get(route('emotions.create'));

        $response->assertStatus(200);
        $response->assertSee('Joie');
    }

    public function test_la_page_d_edition_emotion_est_accessible()
    {
        $this->loginAdmin();
        $emotion = Emotion::factory()->create(['nom' => 'Tristesse']);
        Emotion::factory()->create(['nom' => 'Peur']);

        $response = $this->get(route('emotions.edit', $emotion));

        $response->assertStatus(200);
        $response->assertSee('Tristesse');
        $response->assertSee('Peur');
    }

    public function test_model_emotion_relations_parent_enfant()
    {
        $parent = Emotion::factory()->create(['nom' => 'Joie']);
        $enfant = Emotion::factory()->enfant($parent->id)->create(['nom' => 'Extase']);

        // Test de la relation parent (inverse)
        $this->assertEquals($parent->id, $enfant->parent->id);

        // Test de la relation enfants
        $this->assertTrue($parent->enfants->contains($enfant));
    }
}
