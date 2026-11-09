<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_a_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'API Customer', 'email' => 'customer@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertCreated()->assertJsonStructure(['user' => ['id', 'name', 'email', 'role'], 'token']);
        $this->assertDatabaseHas('users', ['email' => 'customer@example.com']);
    }

    public function test_registration_validation_rejects_duplicate_email_and_short_password(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);
        $this->postJson('/api/register', [
            'name' => 'New Customer', 'email' => 'existing@example.com',
            'password' => 'short', 'password_confirmation' => 'short',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_can_login_read_profile_and_logout(): void
    {
        User::factory()->create(['email' => 'login@example.com', 'password' => 'password123']);
        $login = $this->postJson('/api/login', ['email' => 'login@example.com', 'password' => 'password123']);
        $login->assertOk()->assertJsonStructure(['user', 'token']);
        $token = $login->json('token');

        $this->withToken($token)->getJson('/api/me')->assertOk()->assertJsonPath('user.email', 'login@example.com');
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->withToken($token)->getJson('/api/me')->assertUnauthorized();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'valid@example.com']);
        $this->postJson('/api/login', ['email' => 'valid@example.com', 'password' => 'wrong-password'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }
}
