<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $roleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = new RoleRepository();
    }

    /** @test */
    public function it_can_reassign_users_before_deleting_role()
    {
        // Création du rôle par défaut
        $roleDefaut = Role::create(['libelle' => 'Utilisateur']);
        // Création du rôle à supprimer
        $roleASupprimer = Role::create(['libelle' => 'Ancien Rôle']);

        // Création d'un utilisateur lié au rôle à supprimer
        $user = User::factory()->create(['role_id' => $roleASupprimer->id]);

        $result = $this->roleRepository->deleteAndReassign($roleASupprimer);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('roles', ['id' => $roleASupprimer->id]);

        // Vérification du reclassement
        $this->assertEquals($roleDefaut->id, $user->fresh()->role_id);
    }
}
