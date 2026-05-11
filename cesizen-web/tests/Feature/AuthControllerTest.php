<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['id' => 1], ['libelle' => 'Administrateur']);
        Role::firstOrCreate(['id' => 2], ['libelle' => 'Utilisateur']);
    }

    // ============ LOGIN ============

    public function test_un_utilisateur_peut_se_connecter_via_api()
    {
        $user = User::factory()->create([
            'email'    => 'user@cesizen.fr',
            'password' => bcrypt('password123'),
            'active'   => true,
        ]);

        $response = $this->postJson('/api/login', [
            'email'       => 'user@cesizen.fr',
            'password'    => 'password123',
            'device_name' => 'mobile',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_login_echoue_avec_un_mauvais_mot_de_passe()
    {
        User::factory()->create([
            'email'    => 'user@cesizen.fr',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'       => 'user@cesizen.fr',
            'password'    => 'mauvais',
            'device_name' => 'mobile',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_echoue_avec_un_email_inexistant()
    {
        $response = $this->postJson('/api/login', [
            'email'       => 'inconnu@cesizen.fr',
            'password'    => 'password123',
            'device_name' => 'mobile',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_echoue_sans_les_champs_requis()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password', 'device_name']);
    }

    // ============ REGISTER ============

    public function test_un_utilisateur_peut_sinscrire_via_api()
    {
        $response = $this->postJson('/api/register', [
            'name'        => 'Jean Dupont',
            'email'       => 'jean@cesizen.fr',
            'password'    => 'password123',
            'device_name' => 'mobile',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['user', 'token']);
        $this->assertDatabaseHas('users', ['email' => 'jean@cesizen.fr']);
    }

    public function test_inscription_cree_un_utilisateur_avec_le_role_utilisateur()
    {
        $response = $this->postJson('/api/register', [
            'name'        => 'Marie Martin',
            'email'       => 'marie@cesizen.fr',
            'password'    => 'password123',
            'device_name' => 'mobile',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email'   => 'marie@cesizen.fr',
            'role_id' => 2,
            'active'  => 1,
        ]);
    }

    public function test_inscription_echoue_avec_un_email_deja_utilise()
    {
        User::factory()->create(['email' => 'existant@cesizen.fr']);

        $response = $this->postJson('/api/register', [
            'name'        => 'Doublon',
            'email'       => 'existant@cesizen.fr',
            'password'    => 'password123',
            'device_name' => 'mobile',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_inscription_echoue_sans_les_champs_requis()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'device_name']);
    }

    public function test_inscription_echoue_avec_un_mot_de_passe_trop_court()
    {
        $response = $this->postJson('/api/register', [
            'name'        => 'Court',
            'email'       => 'court@cesizen.fr',
            'password'    => '123',
            'device_name' => 'mobile',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    // ============ UPDATE PASSWORD ============

    public function test_un_utilisateur_peut_changer_son_mot_de_passe()
    {
        $user = User::factory()->create(['password' => bcrypt('ancien_mdp')]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'current_password'          => 'ancien_mdp',
            'new_password'              => 'nouveau_mdp',
            'new_password_confirmation' => 'nouveau_mdp',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Mot de passe mis à jour avec succès']);
    }

    public function test_changement_de_mot_de_passe_echoue_avec_mauvais_ancien_mdp()
    {
        $user = User::factory()->create(['password' => bcrypt('bon_mdp')]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'current_password'          => 'mauvais_mdp',
            'new_password'              => 'nouveau_mdp',
            'new_password_confirmation' => 'nouveau_mdp',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => "L'ancien mot de passe est incorrect"]);
    }

    public function test_changement_de_mot_de_passe_echoue_si_confirmation_incorrecte()
    {
        $user = User::factory()->create(['password' => bcrypt('bon_mdp')]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'current_password'          => 'bon_mdp',
            'new_password'              => 'nouveau_mdp',
            'new_password_confirmation' => 'different',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('new_password');
    }

    public function test_un_utilisateur_non_connecte_ne_peut_pas_changer_son_mdp()
    {
        $response = $this->putJson('/api/user/password', [
            'current_password'          => 'mdp',
            'new_password'              => 'nouveau_mdp',
            'new_password_confirmation' => 'nouveau_mdp',
        ]);

        $response->assertStatus(401);
    }

    // ============ LOGOUT ============

    public function test_un_utilisateur_peut_se_deconnecter_via_api()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Déconnexion réussie.']);
    }

    public function test_un_utilisateur_non_connecte_ne_peut_pas_se_deconnecter()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}
