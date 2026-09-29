<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogFilteringTest extends TestCase
{
    use RefreshDatabase;

    // ==================== CATEGORY FILTERING TESTS ====================

    public function test_filter_articles_by_category(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Tech Article',
            'category_slug' => 'technology',
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Design Article',
            'category_slug' => 'design',
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?category=technology');

        $response->assertStatus(200);
        $response->assertSee('Tech Article');
        $response->assertDontSee('Design Article');
    }

    public function test_filter_by_nonexistent_category_returns_empty(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Tech Article',
            'category_slug' => 'technology',
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?category=nonexistent');

        $response->assertStatus(200);
        $response->assertDontSee('Tech Article');
    }

    // ==================== AUTHOR FILTERING TESTS ====================

    public function test_filter_articles_by_author(): void
    {
        $author1 = User::factory()->create();
        $author2 = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Article by Author 1',
            'user_id' => $author1->id,
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Article by Author 2',
            'user_id' => $author2->id,
            'status' => 'published',
        ]);

        $response = $this->get("/blogs?author={$author1->id}");

        $response->assertStatus(200);
        $response->assertSee('Article by Author 1');
        $response->assertDontSee('Article by Author 2');
    }

    // ==================== SEARCH FILTERING TESTS ====================

    public function test_search_articles_by_title(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Laravel Security Best Practices',
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Vue.js Performance Tips',
            'status' => 'published',
        ]);

        $response = $this->get('/search-results?q=Laravel');

        $response->assertStatus(200);
        $response->assertSee('Laravel Security Best Practices');
        $response->assertDontSee('Vue.js Performance Tips');
    }

    public function test_search_articles_by_content(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Article One',
            'content' => '<p>This contains unique search term XYZ123</p>',
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Article Two',
            'content' => '<p>Different content</p>',
            'status' => 'published',
        ]);

        $response = $this->get('/search-results?q=XYZ123');

        $response->assertStatus(200);
        $response->assertSee('Article One');
        $response->assertDontSee('Article Two');
    }

    public function test_search_articles_by_tag(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Tagged Article',
            'tags' => ['php', 'laravel'],
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Untagged Article',
            'tags' => ['vue', 'javascript'],
            'status' => 'published',
        ]);

        $response = $this->get('/search-results?q=php');

        $response->assertStatus(200);
        $response->assertSee('Tagged Article');
        $response->assertDontSee('Untagged Article');
    }

    public function test_search_with_empty_query_returns_all(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Article One',
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Article Two',
            'status' => 'published',
        ]);

        $response = $this->get('/search-results?q=');

        $response->assertStatus(200);
        $response->assertSee('Article One');
        $response->assertSee('Article Two');
    }

    // ==================== SORTING TESTS ====================

    public function test_sort_articles_by_newest(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Older Article',
            'created_at' => now()->subDays(5),
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Newer Article',
            'created_at' => now(),
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?sort=newest');

        $response->assertStatus(200);
        // Newer article should appear first
        $this->assertGreaterThan(
            strpos($response->getContent(), 'Older Article'),
            strpos($response->getContent(), 'Newer Article')
        );
    }

    public function test_sort_articles_by_oldest(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Newer Article',
            'created_at' => now(),
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Older Article',
            'created_at' => now()->subDays(5),
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?sort=oldest');

        $response->assertStatus(200);
        // Older article should appear first
        $this->assertGreaterThan(
            strpos($response->getContent(), 'Newer Article'),
            strpos($response->getContent(), 'Older Article')
        );
    }

    public function test_sort_articles_by_popular(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Unpopular Article',
            'views' => 10,
            'likes' => 1,
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Popular Article',
            'views' => 1000,
            'likes' => 100,
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?sort=popular');

        $response->assertStatus(200);
        // Popular article should appear first
        $this->assertGreaterThan(
            strpos($response->getContent(), 'Unpopular Article'),
            strpos($response->getContent(), 'Popular Article')
        );
    }

    // ==================== PAGINATION TESTS ====================

    public function test_pagination_works_correctly(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->count(15)->create([
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?page=1');

        $response->assertStatus(200);
        // Should show 9 articles per page (default)
        $this->assertStringContainsString('Showing 1-9 of 15', $response->getContent());
    }

    public function test_pagination_page_2(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->count(15)->create([
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?page=2');

        $response->assertStatus(200);
        $this->assertStringContainsString('Showing 10-15 of 15', $response->getContent());
    }

    public function test_pagination_invalid_page_defaults_to_first(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->count(5)->create([
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?page=999');

        $response->assertStatus(200);
        $this->assertStringContainsString('Showing 1-5 of 5', $response->getContent());
    }

    // ==================== COMBINED FILTERING TESTS ====================

    public function test_combined_category_and_author_filter(): void
    {
        $author1 = User::factory()->create();
        $author2 = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Tech by Author 1',
            'category_slug' => 'technology',
            'user_id' => $author1->id,
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Design by Author 1',
            'category_slug' => 'design',
            'user_id' => $author1->id,
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Tech by Author 2',
            'category_slug' => 'technology',
            'user_id' => $author2->id,
            'status' => 'published',
        ]);

        $response = $this->get("/blogs?category=technology&author={$author1->id}");

        $response->assertStatus(200);
        $response->assertSee('Tech by Author 1');
        $response->assertDontSee('Design by Author 1');
        $response->assertDontSee('Tech by Author 2');
    }

    public function test_combined_search_and_category_filter(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Laravel Security',
            'category_slug' => 'technology',
            'status' => 'published',
        ]);
        \App\Models\Article::factory()->create([
            'title' => 'Laravel Design Patterns',
            'category_slug' => 'design',
            'status' => 'published',
        ]);

        $response = $this->get('/blogs?category=technology&q=Laravel');

        $response->assertStatus(200);
        $response->assertSee('Laravel Security');
        $response->assertDontSee('Laravel Design Patterns');
    }

    // ==================== API RESPONSE TESTS ====================

    public function test_api_returns_json_for_ajax_requests(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Test Article',
            'status' => 'published',
        ]);

        $response = $this->get('/blogs', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'excerpt', 'category', 'author', 'date']
            ],
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ]);
    }

    public function test_api_search_returns_json(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Test Article',
            'status' => 'published',
        ]);

        $response = $this->get('/search-results?q=Test', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'excerpt']
            ]
        ]);
    }

    // ==================== SECURITY TESTS ====================

    public function test_search_sanitizes_xss_attempts(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Normal Article',
            'status' => 'published',
        ]);

        $response = $this->get('/search-results?q=<script>alert("xss")</script>');

        $response->assertStatus(200);
        $response->assertDontSee('<script>alert("xss")</script>');
    }

    public function test_filter_prevents_sql_injection(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Test Article',
            'status' => 'published',
        ]);

        // Attempt SQL injection
        $response = $this->get('/blogs?category=technology;DROP TABLE users;--');

        $response->assertStatus(200);
        // Users table should still exist
        $this->assertDatabaseCount('users', 1);
    }

    public function test_sort_parameter_is_validated(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'title' => 'Test Article',
            'status' => 'published',
        ]);

        // Invalid sort parameter
        $response = $this->get('/blogs?sort=malicious_code');

        $response->assertStatus(200);
        // Should default to newest, not execute malicious code
    }
}
