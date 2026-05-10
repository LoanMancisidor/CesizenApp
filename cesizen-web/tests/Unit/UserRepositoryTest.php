<?php

namespace Tests\Unit;

// IMPORTANT : Utilise le TestCase de Laravel, pas celui de PHPUnit
use Tests\TestCase;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends TestCase
{
    // Vide la base de données à chaque test pour repartir à neuf
    use RefreshDatabase;

    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    /** @test */
    public function it_can_create_a_user()
    {
        // Préparation : il nous faut un rôle en base
        $role = Role::create(['libelle' => 'Utilisateur']);

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role_id' => $role->id
        ];

        $user = $this->userRepository->createUser($data);

        // Assertions
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role_id' => $role->id
        ];

        $updatedUser = $this->userRepository->updateUser($user, $updateData);

        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertDatabaseHas('users', ['email' => 'updated@example.com']);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $result = $this->userRepository->deleteUser($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_can_toggle_user_status()
    {
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::factory()->create(['role_id' => $role->id, 'active' => true]);

        $updatedUser = $this->userRepository->toggleUserStatus($user);

        $this->assertFalse((bool)$updatedUser->active);
    }

    /** @test */
    public function it_can_find_user_by_id()
    {
        // Préparation : on crée un utilisateur en base
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::factory()->create(['role_id' => $role->id]);

        // Action
        $foundUser = $this->userRepository->findById($user->id);

        // Assertions
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($user->email, $foundUser->email);
    }

    /** @test */
    public function it_can_find_user_by_email()
    {
        // Préparation
        $role = Role::create(['libelle' => 'Utilisateur']);
        $email = 'trouve_moi@test.fr';
        $user = User::factory()->create([
            'email' => $email,
            'role_id' => $role->id
        ]);

        // Action
        $foundUser = $this->userRepository->findByEmail($email);

        // Assertions
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($email, $foundUser->email);
        $this->assertEquals($user->id, $foundUser->id);
    }
}
