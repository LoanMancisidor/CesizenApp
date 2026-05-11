<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Emotion;
use App\Models\UserEmotion;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEmotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialisation des rôles pour le bon fonctionnement des factories
        Role::firstOrCreate(['id' => 1], ['libelle' => 'Administrateur']);
        Role::firstOrCreate(['id' => 2], ['libelle' => 'Utilisateur']);
    }

    private function login()
    {
        $user = User::factory()->create(['role_id' => 2]);
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    /**
     * Test de l'enregistrement d'une émotion via l'API
     */
    public function test_un_utilisateur_peut_enregistrer_une_emotion()
    {
        $user = $this->login();
        $emotion = Emotion::create(['nom' => 'Joie', 'image_icone' => 'joie.png']);

        $response = $this->postJson('/api/user-emotions', [
            'emotion_id' => $emotion->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('user_emotions', [
            'user_id' => $user->id,
            'emotion_id' => $emotion->id
        ]);
    }

    /**
     * Test de la récupération de l'historique personnel
     */
    public function test_un_utilisateur_voit_son_propre_historique()
    {
        $user1 = $this->login();
        $emotion = Emotion::create(['nom' => 'Triste']);
        
        // Création d'une entrée pour l'utilisateur connecté
        UserEmotion::create(['user_id' => $user1->id, 'emotion_id' => $emotion->id]);

        // Création d'un autre utilisateur avec son émotion
        $user2 = User::factory()->create(['role_id' => 2]);
        UserEmotion::create(['user_id' => $user2->id, 'emotion_id' => $emotion->id]);

        $response = $this->getJson('/api/user-emotions/history');

        $response->assertStatus(200);
        // On vérifie qu'on ne reçoit qu'une seule entrée (la nôtre)
        $response->assertJsonCount(1);
    }

    /**
     * Test de la suppression d'une entrée du journal
     */
    public function test_un_utilisateur_peut_supprimer_une_entree_de_son_journal()
    {
        $user = $this->login();
        $emotion = Emotion::create(['nom' => 'Colère']);
        $entry = UserEmotion::create(['user_id' => $user->id, 'emotion_id' => $emotion->id]);

        $response = $this->deleteJson("/api/user-emotions/{$entry->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Entrée supprimée']);
        $this->assertDatabaseMissing('user_emotions', ['id' => $entry->id]);
    }

    /**
     * Test des statistiques
     */
    public function test_les_statistiques_renvoient_le_bon_format()
    {
        $user = $this->login();
        $emotion = Emotion::create(['nom' => 'Joie']);
        
        UserEmotion::create(['user_id' => $user->id, 'emotion_id' => $emotion->id]);
        UserEmotion::create(['user_id' => $user->id, 'emotion_id' => $emotion->id]);

        $response = $this->getJson('/api/user-emotions/stats');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'nom' => 'Joie',
            'total' => 2
        ]);
    }
}