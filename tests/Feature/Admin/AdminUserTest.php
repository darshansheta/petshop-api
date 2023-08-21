<?php

namespace Tests\Feature\Admin\AuthTest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AdminUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $adminUser = User::whereEmail(config('app.admin.email'))->firstOrFail();
        $this->actingAs($adminUser);
    }

    /**
     * @test
     */
    public function create_admin_validation_fail(): void
    {
        $response = $this->postJson(route('api.v1.admin.create'), []);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'error',
            'errors' => [
                'email',
                'password',
                'first_name',
                'last_name',
                'password',
                'password_confirmation',
                'address',
                'phone_number',
            ]
        ]);
    }

    /**
     * @test
     */
    public function admin_can_create_an_admin(): void
    {
        $password = $this->faker->password;
        $params = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'password' => $password,
            'password_confirmation' => $password,
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
        ];

        $response = $this->postJson(route('api.v1.admin.create'), $params);

        $response->assertCreated();

        $responseData = $response->json()['data'];

        $this->assertEquals($responseData['email'], $params['email']);
        $this->assertEquals($responseData['first_name'], $params['first_name']);
        $this->assertEquals($responseData['last_name'], $params['last_name']);
        $this->assertEquals($responseData['address'], $params['address']);
        $this->assertEquals($responseData['phone_number'], $params['phone_number']);
        $this->assertNotNull($responseData['uuid']);
        $this->assertNotNull($responseData['token']);
    }

    /**
     * @test
     */
    public function admin_user_listing(): void
    {
        $response = $this->getJson(route('api.v1.admin.user.listing'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data',
            'meta',
            'links',
        ]);

        $responseData = $response->json();

        $this->assertEquals($responseData['meta']['total'], 100); //seeded from seeder;
        $this->assertEquals($responseData['meta']['current_page'], 1);
        $this->assertEquals($responseData['meta']['per_page'], 15);
    }

    /**
     * @test
     */
    public function admin_cannot_edit_or_delete_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => 1]);

        $response = $this->putJson(route('api.v1.admin.user.edit', $admin->uuid), []);

        $response->assertNotFound();

        $response = $this->deleteJson(route('api.v1.admin.user.delete', $admin->uuid));

        $response->assertNotFound();
    }

    /**
     * @test
     */
    public function admin_can_edit_user(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson(route('api.v1.admin.user.edit', $user->uuid), []);

        $response->assertUnprocessable();

        $password = $this->faker->password;

        $email = $this->faker->safeEmail;

        while (User::where('email', $email)->exists()) {
            $email = $this->faker->safeEmail;
        }

        $params = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'address' => $user->address,
            'phone_number' => $user->phone_number,
            'is_marketing' => $user->is_marketing,
        ];

        $response = $this->putJson(route('api.v1.admin.user.edit', $user->uuid), $params);

        $response->assertOk();

        $params['email'] = User::inRandomOrder()->first()->email;
        $response = $this->putJson(route('api.v1.admin.user.edit', $user->uuid), $params);

        $response->assertUnprocessable();

        $response->assertJsonStructure([
            'error',
            'errors' => [
                'email',
            ]
        ]);

        $this->assertEquals($response->json('error'), 'The email has already been taken.');
    }

    /**
     * @test
     */
    public function admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson(route('api.v1.admin.user.delete', $user->uuid));

        $response->assertOk();

        $response->assertJson([
            'success' => 1,
            'message' => 'User deleted'
        ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}