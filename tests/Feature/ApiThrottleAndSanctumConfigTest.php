<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiThrottleAndSanctumConfigTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_sanctum_token_expiration_is_set_to_30_days(): void
    {
        $expiration = config('sanctum.expiration');

        $this->assertSame(43200, $expiration, 'Sanctum expiration must be set to 43200 minutes (30 days).');
    }

    public function test_register_route_is_throttled_at_10_requests_per_minute_with_json_response(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $response = $this->postJson('/api/v1/auth/register', [
                'name' => "User {$i}",
                'username' => "user{$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);
            $response->assertStatus(201);
        }

        // 11th request must be throttled (429)
        $throttled = $this->postJson('/api/v1/auth/register', [
            'name' => 'User 11',
            'username' => 'user11',
            'email' => 'user11@example.com',
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

    public function test_comment_create_is_throttled_at_20_requests_per_minute_with_json_response(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
        ]);

        Sanctum::actingAs($author);

        for ($i = 1; $i <= 20; $i++) {
            $response = $this->postJson("/api/v1/articles/{$article->id}/comments", [
                'content' => "Test comment {$i}",
            ]);
            $response->assertStatus(201);
        }

        // 21st comment must be throttled (429)
        $throttled = $this->postJson("/api/v1/articles/{$article->id}/comments", [
            'content' => 'Test comment 21',
        ]);

        $throttled->assertStatus(429)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure(['success', 'message']);

        $this->assertNotEmpty($throttled->json('message'));
        $this->assertTrue($throttled->headers->has('Retry-After'));
    }

    public function test_article_write_routes_are_throttled_at_60_requests_per_minute_with_json_response(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        // Send 60 article store requests
        for ($i = 1; $i <= 60; $i++) {
            $response = $this->postJson('/api/v1/articles', [
                'title' => "Article Title {$i}",
                'content' => "Article content body for article {$i}",
                'category_slug' => 'technology',
                'status' => 'draft',
            ]);
            $response->assertStatus(201);
        }

        // 61st article write request must be throttled (429)
        $throttled = $this->postJson('/api/v1/articles', [
            'title' => 'Article Title 61',
            'content' => 'Article content body for article 61',
            'category_slug' => 'technology',
            'status' => 'draft',
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
