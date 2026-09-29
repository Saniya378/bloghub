<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    // ==================== COMMENT SUBMISSION TESTS ====================

    public function test_authenticated_user_can_submit_comment(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/comments', [
            'article_id' => $article->id,
            'content' => 'This is a test comment',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'content' => 'This is a test comment',
            'status' => 'pending', // Comments require moderation
        ]);
    }

    public function test_guest_cannot_submit_comment(): void
    {
        $article = \App\Models\Article::factory()->create();

        $response = $this->post('/comments', [
            'article_id' => $article->id,
            'content' => 'This is a test comment',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_comment_requires_content(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/comments', [
            'article_id' => $article->id,
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_comment_requires_valid_article(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/comments', [
            'article_id' => 99999,
            'content' => 'This is a test comment',
        ]);

        $response->assertSessionHasErrors('article_id');
    }

    public function test_comment_content_is_sanitized(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/comments', [
            'article_id' => $article->id,
            'content' => '<script>alert("xss")</script><p>Safe content</p>',
        ]);

        $response->assertStatus(201);
        $comment = \App\Models\Comment::first();
        $this->assertStringNotContainsString('<script>', $comment->content);
        $this->assertStringContainsString('<p>Safe content</p>', $comment->content);
    }

    public function test_comment_max_length_enforced(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/comments', [
            'article_id' => $article->id,
            'content' => str_repeat('a', 1001), // Over 1000 char limit
        ]);

        $response->assertSessionHasErrors('content');
    }

    // ==================== COMMENT MODERATION TESTS ====================

    public function test_new_comments_have_pending_status(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        $this->post('/comments', [
            'article_id' => $article->id,
            'content' => 'Test comment',
        ]);

        $this->assertDatabaseHas('comments', [
            'status' => 'pending',
        ]);
    }

    public function test_pending_comments_not_visible_to_public(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();
        \App\Models\Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'content' => 'Pending comment',
        ]);

        $response = $this->get("/blog-details?slug={$article->slug}");
        $response->assertStatus(200);
        $response->assertDontSee('Pending comment');
    }

    public function test_approved_comments_visible_to_public(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();
        \App\Models\Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'status' => 'approved',
            'content' => 'Approved comment',
        ]);

        $response = $this->get("/blog-details?slug={$article->slug}");
        $response->assertStatus(200);
        $response->assertSee('Approved comment');
    }

    public function test_admin_can_approve_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->patch("/comments/{$comment->id}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->patch("/comments/{$comment->id}/reject");

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'rejected',
        ]);
    }

    public function test_non_admin_cannot_approve_comment(): void
    {
        $user = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'status' => 'pending',
        ]);

        $this->actingAs($user);

        $response = $this->patch("/comments/{$comment->id}/approve");

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_another_users_comment(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->actingAs($otherUser);

        $response = $this->delete("/comments/{$comment->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin);

        $response = $this->delete("/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    // ==================== COMMENT RATE LIMITING ====================

    public function test_comment_submission_rate_limiting(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create();

        $this->actingAs($user);

        // Submit 10 comments quickly (the limit)
        for ($i = 0; $i < 10; $i++) {
            $this->post('/comments', [
                'article_id' => $article->id,
                'content' => "Comment $i",
            ]);
        }

        // 11th comment should be rate limited
        $response = $this->post('/comments', [
            'article_id' => $article->id,
            'content' => 'Rate limited comment',
        ]);

        $response->assertStatus(429);
    }

    // ==================== COMMENT LIKE TESTS ====================

    public function test_user_can_like_comment(): void
    {
        $user = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'likes' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->post("/comments/{$comment->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => true, 'likes' => 1]);
    }

    public function test_user_can_unlike_comment(): void
    {
        $user = User::factory()->create();
        $comment = \App\Models\Comment::factory()->create([
            'likes' => 1,
        ]);

        $this->actingAs($user);
        $this->post("/comments/{$comment->id}/like");

        $response = $this->post("/comments/{$comment->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => false, 'likes' => 0]);
    }
}
