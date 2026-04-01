<?php

namespace Tests\Unit;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_autorise_les_administrateurs()
    {
        $role = Role::create(['libelle' => 'Administrateur']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user);

        $request = Request::create('/admin', 'GET');
        $middleware = new AdminMiddleware();

        $response = $middleware->handle($request, function () {
            return response('Success', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_middleware_bloque_les_utilisateurs_non_admin()
    {
        $role = Role::create(['libelle' => 'Utilisateur']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user);

        $request = Request::create('/admin', 'GET');
        $middleware = new AdminMiddleware();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $middleware->handle($request, function () {});
    }
}
