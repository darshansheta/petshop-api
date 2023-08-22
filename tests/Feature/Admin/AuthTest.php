<?php

namespace Tests\Feature\Admin\AuthTest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function login_validation(): void
    {
        $params = [];

        $response = $this->postJson(route('api.v1.admin.login'), $params);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'error',
            'errors' => [
                'email',
                'password',
            ],
        ]);
    }

    /**
     * @test
     */
    public function login_validation_for_invalid_login_and_password(): void
    {
        $params = [
            'email' => $this->faker->safeEmail,
            'password' => $this->faker->password,
        ];

        $response = $this->postJson(route('api.v1.admin.login'), $params);

        $response->assertStatus(401);

        $response->assertJson([
            'success' => 0,
            'error' => 'Failed to authenticate user',
        ]);
    }

    /**
     * @test
     */
    public function login_success(): void
    {
        $adminEmail = config('app.admin.email');
        $adminPassword = config('app.admin.password');

        $params = [
            'email' => $adminEmail,
            'password' => $adminPassword,
        ];

        $response = $this->postJson(route('api.v1.admin.login'), $params);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'token',
            'success',
        ]);

        $adminUser = User::whereEmail($adminEmail)->firstOrFail();

        $payload = $this->app->make('my-jwt.core')->decode($response->json()['token']);

        $this->assertEquals($payload['sub'], $adminUser->id);
        $this->assertEquals($payload['uid'], $adminUser->uuid);

        $response
            ->assertStatus(200);

        $this->withHeaders([
                'Authorization' => 'Bearer ' . $response->json()['token'],
            ])
            ->getJson(route('api.v1.admin.user.listing'))
            ->assertOk();
    }

    /**
     * @test
     */
    public function logout_success(): void
    {
        $adminEmail = config('app.admin.email');
        $adminPassword = config('app.admin.password');

        $params = [
            'email' => $adminEmail,
            'password' => $adminPassword,
        ];

        $response = $this->postJson(route('api.v1.admin.login'), $params);

        $response->assertStatus(200);

        $adminUser = User::whereEmail($adminEmail)->firstOrFail();

        // $this->actingAs($adminUser);

        $response = $this->withHeaders([
                    'Authorization' => 'Bearer ' . $response->json()['token'],
                ])
                ->deleteJson(route('api.v1.admin.logout'));

        $response->assertStatus(200);

        $response->assertJson([
            'success' => 1,
            'message' => 'Token Revoked',
        ]);
    }
}
