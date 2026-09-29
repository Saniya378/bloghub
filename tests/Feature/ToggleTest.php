<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleTest extends TestCase
{
    use RefreshDatabase;

    // ==================== LIKE TOGGLE TESTS ====================

    public function test_authenticated_user_can_like_article(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'likes' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->post("/articles/{$article->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => true, 'likes' => 1]);
        $this->assertDatabaseHas('article_likes', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_authenticated_user_can_unlike_article(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'likes' => 1,
        ]);

        // First like
        $this->actingAs($user);
        $this->post("/articles/{$article->id}/like");

        // Then unlike
        $response = $this->post("/articles/{$article->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => false, 'likes' => 0]);
        $this->assertDatabaseMissing('article_likes', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_guest_cannot_like_article(): void
    {
        $article = \App\Models\Article::factory()->create();

        $response = $this->post("/articles/{$article->id}/like");

        $response->assertRedirect('/login');
    }

    public function test_like_count_increments_correctly(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'likes' => 0,
        ]);

        $this->actingAs($user1);
        $this->post("/articles/{$article->id}/like");

        $this->actingAs($user2);
        $this->post("/articles/{$article->id}/like");

        $article->refresh();
        $this->assertEquals(2, $article->likes);
    }

    // ==================== BOOKMARK TOGGLE TESTS ====================

    public function test_authenticated_user_can_bookmark_article(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/articles/{$article->id}/bookmark");

        $response->assertStatus(200);
        $response->assertJson(['bookmarked' => true]);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_authenticated_user_can_remove_bookmark(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);
        $this->post("/articles/{$article->id}/bookmark");

        $response = $this->post("/articles/{$article->id}/bookmark");

        $response->assertStatus(200);
        $response->assertJson(['bookmarked' => false]);
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_guest_cannot_bookmark_article(): void
    {
        $article = \App\Models\Article::factory()->create();

        $response = $this->post("/articles/{$article->id}/bookmark");

        $response->assertRedirect('/login');
    }

    // ==================== FOLLOW TOGGLE TESTS ====================

    public function test_authenticated_user_can_follow_author(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/authors/{$author->id}/follow");

        $response->assertStatus(200);
        $response->assertJson(['following' => true]);
        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'author_id' => $author->id,
        ]);
    }

    public function test_authenticated_user_can_unfollow_author(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();

        $this->actingAs($user);
        $this->post("/authors/{$author->id}/follow");

        $response = $this->post("/authors/{$author->id}/follow");

        $response->assertStatus(200);
        $response->assertJson(['following' => false]);
        $this->assertDatabaseMissing('follows', [
            'user_id' => $user->id,
            'author_id' => $author->id,
        ]);
    }

    public function test_guest_cannot_follow_author(): void
    {
        $author = User::factory()->create();

        $response = $this->post("/authors/{$author->id}/follow");

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_follow_themselves(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/authors/{$user->id}/follow");

        $response->assertStatus(400);
        $response->assertJson(['error' => 'You cannot follow yourself']);
    }

    public function test_follow_count_updates_correctly(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $author = User::factory()->create([
            'followers' => 0,
        ]);

        $this->actingAs($user1);
        $this->post("/authors/{$author->id}/follow");

        $this->actingAs($user2);
        $this->post("/authors/{$author->id}/follow");

        $author->refresh();
        $this->assertEquals(2, $author->followers);
    }

    // ==================== TOGGLE IDEMPOTENCY TESTS ====================

    public function test_like_is_idempotent_per_user(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'likes' => 0,
        ]);

        $this->actingAs($user);

        // Like twice quickly
        $this->post("/articles/{$article->id}/like");
        $this->post("/articles/{$article->id}/like"); // Unlike
        $this->post("/articles/{$article->id}/like"); // Like again

        $article->refresh();
        $this->assertEquals(1, $article->likes);
    }

    public function test_bookmark_is_idempotent_per_user(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        $this->post("/articles/{$article->id}/bookmark");
        $this->post("/articles/{$article->id}/bookmark");
        $this->post("/articles/{$article->id}/bookmark");

        $this->assertDatabaseCount('bookmarks', 1);
    }
}
