<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorCommentModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_comment_status_is_pending_and_not_counted_in_public_count(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $reader = User::factory()->create(['role' => 'reader']);

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
            'comment_count' => 0,
        ]);

        Sanctum::actingAs($reader);

        $response = $this->postJson("/api/v1/articles/{$article->id}/comments", [
            'content' => 'This is a comment by a reader that requires moderation.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Comment submitted for moderation.',
                'data' => [
                    'status' => 'pending',
                    'content' => 'This is a comment by a reader that requires moderation.',
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $reader->id,
            'status' => 'pending',
        ]);

        // Public comment_count on article table remains 0
        $this->assertSame(0, $article->fresh()->comment_count);
    }

    public function test_author_and_admin_comments_are_auto_approved(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $admin = User::factory()->create(['role' => 'admin']);

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
            'comment_count' => 0,
        ]);

        // Author comment
        Sanctum::actingAs($author);
        $resAuthor = $this->postJson("/api/v1/articles/{$article->id}/comments", [
            'content' => 'Author response here.',
        ]);

        $resAuthor->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Comment posted successfully.',
                'data' => [
                    'status' => 'approved',
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $author->id,
            'status' => 'approved',
        ]);
        $this->assertSame(1, $article->fresh()->comment_count);

        // Admin comment
        Sanctum::actingAs($admin);
        $resAdmin = $this->postJson("/api/v1/articles/{$article->id}/comments", [
            'content' => 'Admin announcement here.',
        ]);

        $resAdmin->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Comment posted successfully.',
                'data' => [
                    'status' => 'approved',
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $admin->id,
            'status' => 'approved',
        ]);
        $this->assertSame(2, $article->fresh()->comment_count);
    }

    public function test_public_comment_endpoints_count_and_show_only_approved_comments(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $user1 = User::factory()->create(['name' => 'Approved User']);
        $user2 = User::factory()->create(['name' => 'Pending User']);
        $user3 = User::factory()->create(['name' => 'Rejected User']);

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
            'comment_count' => 1,
        ]);

        Comment::create([
            'user_id' => $user1->id,
            'article_id' => $article->id,
            'content' => 'Approved comment content',
            'status' => 'approved',
        ]);

        Comment::create([
            'user_id' => $user2->id,
            'article_id' => $article->id,
            'content' => 'Pending comment content',
            'status' => 'pending',
        ]);

        Comment::create([
            'user_id' => $user3->id,
            'article_id' => $article->id,
            'content' => 'Rejected comment content',
            'status' => 'rejected',
        ]);

        // 1. GET /api/v1/articles/{id}/comments
        $listRes = $this->getJson("/api/v1/articles/{$article->id}/comments");
        $listRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 1,
            ]);

        $this->assertCount(1, $listRes->json('data'));
        $this->assertSame('Approved comment content', $listRes->json('data.0.content'));

        // 2. GET /api/v1/articles/{id}
        $detailRes = $this->getJson("/api/v1/articles/{$article->id}");
        $detailRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'comment_count' => 1,
                    'comments_count' => 1,
                ],
            ]);

        $this->assertCount(1, $detailRes->json('data.comments'));
        $this->assertSame('Approved comment content', $detailRes->json('data.comments.0.content'));
    }

    public function test_author_comments_status_filter_and_10_per_page_pagination(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $commenter = User::factory()->create();

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        // Create 12 pending comments
        for ($i = 1; $i <= 12; $i++) {
            Comment::create([
                'user_id' => $commenter->id,
                'article_id' => $article->id,
                'content' => "Pending comment #{$i}",
                'status' => 'pending',
                'created_at' => now()->subMinutes(20 - $i),
            ]);
        }

        // Create 3 approved comments
        for ($j = 1; $j <= 3; $j++) {
            Comment::create([
                'user_id' => $commenter->id,
                'article_id' => $article->id,
                'content' => "Approved comment #{$j}",
                'status' => 'approved',
                'created_at' => now()->subMinutes(5 - $j),
            ]);
        }

        Sanctum::actingAs($author);

        // Default pagination: 10 per page
        $allRes = $this->getJson('/api/v1/author/comments');
        $allRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 15,
                    'has_more' => true,
                ],
            ]);
        $this->assertCount(10, $allRes->json('data'));

        // Filter: status=pending
        $pendingRes = $this->getJson('/api/v1/author/comments?status=pending');
        $pendingRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'pagination' => [
                    'per_page' => 10,
                    'total' => 12,
                    'has_more' => true,
                ],
            ]);
        $this->assertCount(10, $pendingRes->json('data'));
        foreach ($pendingRes->json('data') as $item) {
            $this->assertSame('pending', $item['status']);
        }

        // Filter: status=approved
        $approvedRes = $this->getJson('/api/v1/author/comments?status=approved');
        $approvedRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'pagination' => [
                    'per_page' => 10,
                    'total' => 3,
                    'has_more' => false,
                ],
            ]);
        $this->assertCount(3, $approvedRes->json('data'));
        foreach ($approvedRes->json('data') as $item) {
            $this->assertSame('approved', $item['status']);
        }
    }

    public function test_author_comments_includes_all_required_fields(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $commenter = User::factory()->create([
            'name' => 'Alice Johnson',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Building Fast Flutter Apps',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $comment = Comment::create([
            'user_id' => $commenter->id,
            'article_id' => $article->id,
            'content' => 'Outstanding tutorial on state management!',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($author);

        $res = $this->getJson('/api/v1/author/comments');
        $res->assertStatus(200);

        $item = $res->json('data.0');
        $this->assertNotNull($item);

        // Required fields: commenter name, avatar, article id, article title, content, status, created_at
        $this->assertSame('Alice Johnson', $item['commenter_name']);
        $this->assertSame('Alice Johnson', $item['user']['name']);
        $this->assertSame('https://example.com/avatar.jpg', $item['commenter_avatar']);
        $this->assertSame('https://example.com/avatar.jpg', $item['user']['avatar']);
        $this->assertSame($article->id, $item['article_id']);
        $this->assertSame($article->id, $item['article']['id']);
        $this->assertSame('Building Fast Flutter Apps', $item['article_title']);
        $this->assertSame('Building Fast Flutter Apps', $item['article']['title']);
        $this->assertSame('Outstanding tutorial on state management!', $item['content']);
        $this->assertSame('pending', $item['status']);
        $this->assertNotNull($item['created_at']);
    }

    public function test_another_author_gets_403_when_approving_or_deleting_comment(): void
    {
        $authorA = User::factory()->create(['role' => 'author']);
        $authorB = User::factory()->create(['role' => 'author']);
        $reader = User::factory()->create(['role' => 'reader']);

        $article = Article::factory()->create([
            'user_id' => $authorA->id,
            'status' => Article::STATUS_PUBLISHED,
            'comment_count' => 0,
        ]);

        $comment = Comment::create([
            'user_id' => $reader->id,
            'article_id' => $article->id,
            'content' => 'Review comment awaiting author approval',
            'status' => 'pending',
        ]);

        // Another author (Author B) tries to approve -> 403 Forbidden
        Sanctum::actingAs($authorB);
        $failApprove = $this->postJson("/api/v1/author/comments/{$comment->id}/approve");
        $failApprove->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You can only moderate comments on your own articles.',
            ]);

        // Another author (Author B) tries to delete -> 403 Forbidden
        $failDelete = $this->deleteJson("/api/v1/author/comments/{$comment->id}");
        $failDelete->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You can only delete comments on your own articles.',
            ]);

        // Reader tries to approve / delete -> 403 Forbidden
        Sanctum::actingAs($reader);
        $this->postJson("/api/v1/author/comments/{$comment->id}/approve")->assertStatus(403);
        $this->deleteJson("/api/v1/author/comments/{$comment->id}")->assertStatus(403);

        // Comment remains intact and pending
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'pending',
        ]);
        $this->assertSame(0, $article->fresh()->comment_count);
    }

    public function test_author_can_approve_and_delete_comment_on_own_article_and_public_count_updates(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $reader = User::factory()->create(['role' => 'reader']);

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
            'comment_count' => 0,
        ]);

        $comment = Comment::create([
            'user_id' => $reader->id,
            'article_id' => $article->id,
            'content' => 'Pending comment to approve then delete',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($author);

        // 1. Approve comment
        $approveRes = $this->postJson("/api/v1/author/comments/{$comment->id}/approve");
        $approveRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Comment approved successfully.',
            ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'approved',
        ]);
        $this->assertSame(1, $article->fresh()->comment_count);

        // 2. Delete comment
        $deleteRes = $this->deleteJson("/api/v1/author/comments/{$comment->id}");
        $deleteRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
        $this->assertSame(0, $article->fresh()->comment_count);
    }

    public function test_admin_can_approve_and_delete_comments_on_any_article(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $admin = User::factory()->create(['role' => 'admin']);
        $reader = User::factory()->create(['role' => 'reader']);

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
            'comment_count' => 0,
        ]);

        $comment = Comment::create([
            'user_id' => $reader->id,
            'article_id' => $article->id,
            'content' => 'Comment on author post moderated by admin',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);

        // Admin approves
        $approveRes = $this->postJson("/api/v1/author/comments/{$comment->id}/approve");
        $approveRes->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertSame(1, $article->fresh()->comment_count);

        // Admin deletes
        $deleteRes = $this->deleteJson("/api/v1/author/comments/{$comment->id}");
        $deleteRes->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertSame(0, $article->fresh()->comment_count);
    }
}
