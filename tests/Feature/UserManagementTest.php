<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_liste_utilisateurs_affichage()
    {
        $this->loginAdmin();
        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('Admin User');
    }

    public function test_creation_utilisateur_reussie()
    {
        $this->loginAdmin();
        $roleUser = Role::create(['libelle' => 'Utilisateur']);

        $response = $this->post(route('users.store'), [
            'name' => 'Nouveau Gars',
            'email' => 'nouveau@test.fr',
            'role_id' => $roleUser->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'nouveau@test.fr']);
    }

    public function test_edition_affiche_les_donnees()
    {
        $admin = $this->loginAdmin();
        $response = $this->get(route('users.edit', $admin));

        $response->assertStatus(200);
        $response->assertSee($admin->email);
    }

    public function test_mise_a_jour_sans_mot_de_passe()
    {
        $admin = $this->loginAdmin();
        $target = User::factory()->create(['role_id' => $admin->role_id]);

        $response = $this->put(route('users.update', $target), [
            'name' => 'Nom Modifié',
            'email' => 'modified@test.fr',
            'role_id' => $admin->role_id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Nom Modifié'
        ]);
    }

    public function test_un_admin_ne_peut_pas_modifier_son_propre_role()
    {
        $admin = $this->loginAdmin();
        $roleUser = Role::create(['libelle' => 'Utilisateur']);

        // Tentative de s'auto-rétrograder en "Utilisateur"
        $response = $this->put(route('users.update', $admin), [
            'name' => 'Admin',
            'email' => 'admin@test.fr',
            'role_id' => $roleUser->id, // On tente de changer le rôle
        ]);

        $response->assertSessionHasErrors('role_id');
        $this->assertEquals($admin->role_id, $admin->fresh()->role_id);
    }

    public function test_toggle_status_active_et_desactive()
    {
        $this->loginAdmin();
        $target = User::factory()->create(['active' => true]);

        // Désactivation
        $this->patch(route('users.toggle', $target));

        // Au lieu de assertFalse, on vérifie que la valeur est bien 0 (ou false)
        $this->assertEquals(0, $target->fresh()->active);
        // OU encore mieux (plus lisible) :
        $this->assertFalse((bool)$target->fresh()->active);

        // Réactivation
        $this->patch(route('users.toggle', $target));
        $this->assertEquals(1, $target->fresh()->active);
    }

    public function test_suppression_utilisateur_ajax()
    {
        $this->loginAdmin();
        $target = User::factory()->create();

        $response = $this->delete(route('users.destroy', $target));

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    /**
     * Test de l'accessibilité de la page de création
     */
    public function test_la_page_de_creation_utilisateur_est_accessible()
    {
        $this->loginAdmin();
        Role::create(['libelle' => 'Utilisateur']); // Pour que Role::all() ne soit pas vide

        $response = $this->get(route('users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('users.create');
        $response->assertViewHas('roles');
    }

    /**
     * Test de la mise à jour avec changement de mot de passe
     */
    public function test_un_admin_peut_modifier_le_mot_de_passe_dun_utilisateur()
    {
        $admin = $this->loginAdmin();
        $target = User::factory()->create(['password' => Hash::make('ancien_mdp')]);

        $response = $this->put(route('users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'role_id' => $target->role_id,
            'password' => 'nouveau_mdp_123',
            'password_confirmation' => 'nouveau_mdp_123',
        ]);

        $response->assertRedirect(route('users.index'));

        // On vérifie que le nouveau mot de passe fonctionne
        $this->assertTrue(Hash::check('nouveau_mdp_123', $target->fresh()->password));
    }
}
