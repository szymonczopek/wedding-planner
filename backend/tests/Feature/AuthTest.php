<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_user_and_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jan Kowalski',
            'email' => 'example1@wp.pl',
            'password' => 'aaaabbbb',
            'password_confirmation' => 'aaaabbbb',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonPath('user.email', 'example1@wp.pl');

        $this->assertDatabaseHas('users', ['email' => 'example1@wp.pl']);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_returns_token_and_me_works_with_bearer(): void
    {
        User::create([
            'name' => 'Jan Kowalski',
            'email' => 'example1@wp.pl',
            'password' => 'aaaabbbb',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'example1@wp.pl',
            'password' => 'aaaabbbb',
        ]);

        $login->assertOk()
            ->assertJsonStructure(['user' => ['id', 'email'], 'token']);

        $token = $login->json('token');
        $this->assertNotEmpty($token);

        $this->getJson('/api/me', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('email', 'example1@wp.pl');
    }

    public function test_login_with_bad_credentials_fails(): void
    {
        User::create([
            'name' => 'Jan Kowalski',
            'email' => 'example1@wp.pl',
            'password' => 'aaaabbbb',
        ]);

        $this->postJson('/api/login', [
            'email' => 'example1@wp.pl',
            'password' => 'wrongpass',
        ])->assertStatus(422);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'example1@wp.pl',
            'password' => 'aaaabbbb',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $headers = ['Authorization' => "Bearer {$token}"];

        $this->getJson('/api/me', $headers)->assertOk();

        $this->postJson('/api/logout', [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_me_rejects_unknown_token(): void
    {
        $this->getJson('/api/me', ['Authorization' => 'Bearer 999|bogus'])
            ->assertUnauthorized();
    }
}
