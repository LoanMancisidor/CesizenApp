<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un rôle simple
     */
    public function test_un_admin_peut_creer_un_role()
    {
        $this->loginAdmin();

        $response = $this->post(route('roles.store'), [
            'libelle' => 'Moderateur'
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['libelle' => 'Moderateur']);
    }

    /**
     * Test de la protection des rôles systèmes (Administrateur/Utilisateur)
     */
    public function test_impossible_de_supprimer_un_role_systeme()
    {
        $this->loginAdmin();

        $roleAdmin = Role::where('libelle', 'Administrateur')->first();

        $response = $this->delete(route('roles.destroy', $roleAdmin));

        $response->assertStatus(403);
        $response->assertJson(['success' => false, 'message' => 'Impossible de supprimer un rôle système.']);

        $this->assertDatabaseHas('roles', ['id' => $roleAdmin->id]);
    }

    /**
     * Test du reclassement des utilisateurs lors de la suppression
     */
    public function test_la_suppression_dun_role_reclasse_les_utilisateurs_en_utilisateur()
    {
        $this->loginAdmin();

        $roleDefaut = Role::create(['libelle' => 'Utilisateur']);
        $roleASupprimer = Role::create(['libelle' => 'Stagiaire']);

        $user = User::factory()->create([
            'name' => 'Jean Stage',
            'role_id' => $roleASupprimer->id
        ]);

        $response = $this->delete(route('roles.destroy', $roleASupprimer));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('roles', ['id' => $roleASupprimer->id]);

        $user->refresh();
        $this->assertEquals($roleDefaut->id, $user->role_id);
        $this->assertEquals('Utilisateur', $user->role->libelle);
    }

    /**
     * Test de sécurité : erreur 500 si le rôle par défaut est manquant
     */
    public function test_erreur_si_le_role_utilisateur_est_introuvable_lors_de_la_suppression()
    {
        $this->loginAdmin();

        $roleTest = Role::create(['libelle' => 'TestRole']);
        Role::where('libelle', 'Utilisateur')->delete();

        $response = $this->delete(route('roles.destroy', $roleTest));

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Erreur : Le rôle "Utilisateur" par défaut est introuvable.']);
    }

    /**
     * Test de l'affichage de la liste des rôles
     */
    public function test_index_affiche_la_liste_des_roles_avec_utilisateurs()
    {
        $this->loginAdmin();

        // On crée un rôle et un utilisateur associé pour tester le "with('users')"
        $role = Role::create(['libelle' => 'Testeur']);
        User::factory()->create(['role_id' => $role->id]);

        $response = $this->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    /**
     * Test de l'accessibilité du formulaire de création
     */
    public function test_la_page_create_est_accessible()
    {
        $this->loginAdmin();

        $response = $this->get(route('roles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('roles.create');
    }
}
