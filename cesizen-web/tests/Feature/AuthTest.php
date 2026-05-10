<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_de_connexion_est_accessible()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Connexion');
    }

    public function test_un_utilisateur_peut_se_connecter_avec_des_identifiants_valides()
    {
        $role = Role::create(['libelle' => 'Administrateur']);
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@cesizen.fr',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@cesizen.fr',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_un_utilisateur_avec_un_mauvais_mot_de_passe_est_rejete()
    {
        $role = Role::create(['libelle' => 'Administrateur']);
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@cesizen.fr',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@cesizen.fr',
            'password' => 'mauvais-mdp',
        ]);

        $this->assertGuest(); // Vérifie que l'utilisateur n'est PAS connecté
    }

    public function test_un_utilisateur_desactive_ne_peut_pas_se_connecter()
    {
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::create([
            'name' => 'User Bloque',
            'email' => 'bloque@cesizen.fr',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'bloque@cesizen.fr',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_un_utilisateur_peut_se_deconnecter()
    {
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::create([
            'name' => 'User Test',
            'email' => 'test@cesizen.fr',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);
        
        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_un_utilisateur_basique_est_bloque_sur_les_routes_admin()
    {
        $roleUser = \App\Models\Role::create(['libelle' => 'Utilisateur']);
        
        $user = \App\Models\User::create([
            'name' => 'Jean Durand',
            'email' => 'jean@test.fr',
            'password' => bcrypt('password'),
            'role_id' => $roleUser->id,
            'active' => true
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(403);
    }
}