<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\AuthRepository;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $authRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authRepository = new AuthRepository();
        Role::create(['id' => 1, 'libelle' => 'Administrateur']);
        Role::create(['id' => 2, 'libelle' => 'Utilisateur']);
    }

    /** @test */
    public function it_can_find_a_user_by_email()
    {
        $user = User::factory()->create(['email' => 'test@cesizen.fr']);

        $foundUser = $this->authRepository->findByEmail('test@cesizen.fr');

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals('test@cesizen.fr', $foundUser->email);
    }

    /** @test */
    public function it_returns_null_when_email_not_found()
    {
        $foundUser = $this->authRepository->findByEmail('inconnu@cesizen.fr');

        $this->assertNull($foundUser);
    }

    /** @test */
    public function it_can_create_a_user_with_default_role_and_active()
    {
        $data = [
            'name'        => 'Nouvel Utilisateur',
            'email'       => 'nouveau@cesizen.fr',
            'password'    => 'password123',
            'device_name' => 'mobile',
        ];

        $user = $this->authRepository->createUser($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Nouvel Utilisateur', $user->name);
        $this->assertEquals('nouveau@cesizen.fr', $user->email);
        $this->assertEquals(2, $user->role_id);
        $this->assertEquals(1, $user->active);
        $this->assertDatabaseHas('users', ['email' => 'nouveau@cesizen.fr']);
    }

    /** @test */
    public function it_hashes_the_password_on_create()
    {
        $data = [
            'name'        => 'User Hash',
            'email'       => 'hash@cesizen.fr',
            'password'    => 'motdepasse',
            'device_name' => 'mobile',
        ];

        $user = $this->authRepository->createUser($data);

        $this->assertNotEquals('motdepasse', $user->password);
        $this->assertTrue(Hash::check('motdepasse', $user->password));
    }

    /** @test */
    public function it_can_update_a_user_password()
    {
        $user = User::factory()->create(['password' => Hash::make('ancien_mdp')]);

        $result = $this->authRepository->updatePassword($user, 'nouveau_mdp');

        $this->assertTrue($result);
        $this->assertTrue(Hash::check('nouveau_mdp', $user->fresh()->password));
    }

    /** @test */
    public function it_hashes_the_new_password_on_update()
    {
        $user = User::factory()->create(['password' => Hash::make('ancien_mdp')]);

        $this->authRepository->updatePassword($user, 'super_secret');

        $this->assertNotEquals('super_secret', $user->fresh()->password);
        $this->assertTrue(Hash::check('super_secret', $user->fresh()->password));
    }
}
