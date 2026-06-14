<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email']]]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_login_fails_with_bad_credentials(): void
    {
        User::factory()->create(['email' => 'real@example.com']);

        $this->postJson('/api/login', [
            'email'    => 'real@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(422);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['message' => 'Logged out successfully.']);
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson('/api/products')->assertStatus(401);
    }
}
