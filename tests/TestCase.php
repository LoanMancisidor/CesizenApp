<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    protected function loginAdmin(): User
    {
        $role = Role::create(['libelle' => 'Administrateur']);
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.fr',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'active' => true
        ]);
        $this->actingAs($user);
        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }
}
