<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiRegisterAuthorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_author_registration_success_creates_author_and_token_works_on_auth_me(): void
    {
        $response = $this->postJson('/api/v1/auth/register-author', [
            'name' => 'Sarah Connor',
            'email' => '  Sarah.Connor@Example.COM  ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'name' => 'Sarah Connor',
                        'email' => 'sarah.connor@example.com',
                        'role' => 'author',
                        'is_active' => true,
                    ],
                    'token_type' => 'Bearer',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'username', 'email', 'role', 'is_active'],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Sarah Connor',
            'email' => 'sarah.connor@example.com',
            'role' => 'author',
            'is_active' => true,
        ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        // Verify token works on GET /api/v1/auth/me
        $meResponse = $this->withToken($token)->getJson('/api/v1/auth/me');
        $meResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'sarah.connor@example.com',
                        'role' => 'author',
                    ],
                ],
            ]);
    }

    public function test_author_registration_duplicate_email_returns_422(): void
    {
        User::factory()->create([
            'email' => 'sarah.connor@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/register-author', [
            'name' => 'Another Sarah',
            'email' => '  SARAH.CONNOR@example.com  ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error',
            ])
            ->assertJsonValidationErrors(['email']);
    }

    public function test_author_registration_short_password_returns_422(): void
    {
        $response = $this->postJson('/api/v1/auth/register-author', [
            'name' => 'Sarah Connor',
            'email' => 'sarah.connor@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error',
            ])
            ->assertJsonValidationErrors(['password']);
    }

    public function test_author_registration_missing_confirmation_returns_422(): void
    {
        $response = $this->postJson('/api/v1/auth/register-author', [
            'name' => 'Sarah Connor',
            'email' => 'sarah.connor@example.com',
            'password' => 'password123',
            // password_confirmation is omitted
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error',
            ])
            ->assertJsonValidationErrors(['password']);
    }

    public function test_author_registration_route_is_throttled_at_10_requests_per_minute(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $res = $this->postJson('/api/v1/auth/register-author', [
                'name' => "Author {$i}",
                'email' => "author{$i}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);
            $res->assertStatus(201);
        }

        // 11th request must be throttled (429)
        $throttled = $this->postJson('/api/v1/auth/register-author', [
            'name' => 'Author 11',
            'email' => 'author11@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $throttled->assertStatus(429)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure(['success', 'message']);

        $this->assertNotEmpty($throttled->json('message'));
        $this->assertTrue($throttled->headers->has('Retry-After'));
    }
}
