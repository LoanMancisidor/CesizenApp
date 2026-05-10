<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_echoue_si_le_compte_est_desactive()
    {
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::factory()->create([
            'email' => 'login@test.fr',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'active' => false // COMPTE DÉSACTIVÉ
        ]);

        // On simule la requête de login
        $request = new LoginRequest();
        $request->merge([
            'email' => 'login@test.fr',
            'password' => 'password123'
        ]);

        // On attend l'exception de validation spécifique à ton code
        $this->expectException(ValidationException::class);

        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors());
            $this->assertEquals('Votre compte est désactivé. Veuillez contacter un administrateur.', $e->errors()['email'][0]);
            throw $e;
        }
    }

    public function test_login_request_rules()
    {
        $request = new LoginRequest();
        $this->assertEquals([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], $request->rules());
    }
}
