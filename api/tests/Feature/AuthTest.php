<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user_on_valid_credentials()
    {
        User::factory()->create(['email' => 'user@example.com','password' => Hash::make('password')]);

        $response = $this->postJson('/api/login', ['email' => 'user@example.com','password' => 'password']);

        $response->assertOk()->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email'],
                'access_token',
            ],
        ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        User::factory()->create(['email' => 'user@example.com','password' => Hash::make('password')]);

        $response = $this->postJson('/api/login', ['email' => 'user@example.com','password' => 'wrong-password']);

        $response->assertStatus(401)->assertJson(['message' => 'Invalid credentials']);
    }

    public function test_logout_deletes_current_token()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertOk()->assertJson(['message' => 'Logged out successfully']);
    }
}
