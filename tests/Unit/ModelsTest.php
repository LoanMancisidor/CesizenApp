<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Models\Emotion;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_is_admin_helper()
    {
        $roleAdmin = Role::create(['libelle' => 'Administrateur']);
        $roleUser = Role::create(['libelle' => 'Utilisateur']);

        $admin = User::factory()->create(['role_id' => $roleAdmin->id]);
        $user = User::factory()->create(['role_id' => $roleUser->id]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    public function test_emotion_has_parent_and_children_relations()
    {
        $parent = Emotion::factory()->create(['nom' => 'Joie']);
        $enfant = Emotion::factory()->create(['nom' => 'Extase', 'parent_id' => $parent->id]);

        // Test relation parent
        $this->assertEquals($parent->id, $enfant->parent->id);
        // Test relation enfants
        $this->assertTrue($parent->enfants->contains($enfant));
    }

    public function test_role_has_many_users_relation()
    {
        $role = Role::create(['libelle' => 'Test']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($role->users->contains($user));
    }
}
