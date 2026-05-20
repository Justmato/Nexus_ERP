<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@erp.local',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole('admin');

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@erp.local',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success', 'data' => ['access_token', 'user'],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong@erp.local',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }
}
