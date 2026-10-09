<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadedCommentsTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(array $attrs = []): Article
    {
        return Article::factory()->create(array_merge(['status' => 'published'], $attrs));
    }

    private function approvedComment(Article $article, array $attrs = []): Comment
    {
        return Comment::factory()->create(array_merge([
            'article_id' => $article->id,
            'status' => 'approved',
            'parent_id' => null,
        ], $attrs));
    }

    // ==================== REPLIES ====================

    public function test_user_can_reply_to_an_approved_comment(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $parent = $this->approvedComment($article);

        $this->actingAs($user);

        $response = $this->postJson('/comments', [
            'article_id' => $article->id,
            'parent_id' => $parent->id,
            'content' => 'A threaded reply',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_reply', true);

        $this->assertDatabaseHas('comments', [
            'parent_id' => $parent->id,
            'content' => 'A threaded reply',
            'status' => 'pending',
        ]);
    }

    public function test_replies_can_nest_without_a_depth_limit(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();

        $this->actingAs($user);

        $root = $this->approvedComment($article);
        $parent = $root;

        // Build a six level deep chain.
        for ($depth = 1; $depth <= 6; $depth++) {
            $this->postJson('/comments', [
                'article_id' => $article->id,
                'parent_id' => $parent->id,
                'content' => "Reply level {$depth}",
            ])->assertStatus(201);

            $parent = Comment::where('content', "Reply level {$depth}")->firstOrFail();
            $parent->update(['status' => 'approved']);
        }

        $this->assertSame(0, $root->fresh()->depth());
        $this->assertSame(6, $parent->fresh()->depth());
    }

    public function test_reply_to_comment_from_another_article_is_rejected(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $otherArticle = $this->publishedArticle();
        $foreignParent = $this->approvedComment($otherArticle);

        $this->actingAs($user);

        $response = $this->postJson('/comments', [
            'article_id' => $article->id,
            'parent_id' => $foreignParent->id,
            'content' => 'Cross article reply',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('comments', ['content' => 'Cross article reply']);
    }

    public function test_cannot_reply_to_a_pending_comment(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $pending = $this->approvedComment($article, ['status' => 'pending']);

        $this->actingAs($user);

        $this->postJson('/comments', [
            'article_id' => $article->id,
            'parent_id' => $pending->id,
            'content' => 'Reply to hidden comment',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('comments', ['content' => 'Reply to hidden comment']);
    }

    public function test_reply_increments_the_article_comment_count(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $parent = $this->approvedComment($article);

        $this->actingAs($user);

        $this->postJson('/comments', [
            'article_id' => $article->id,
            'parent_id' => $parent->id,
            'content' => 'Counted reply',
        ])->assertStatus(201);

        $this->assertSame(1, $article->fresh()->comment_count);
    }

    // ==================== THREAD RENDERING ====================

    public function test_thread_endpoint_returns_the_reply_total(): void
    {
        $article = $this->publishedArticle();
        $root = $this->approvedComment($article);
        $this->approvedComment($article, ['parent_id' => $root->id]);

        $this->getJson('/comments/' . $article->id . '/thread')
            ->assertStatus(200)
            ->assertJsonPath('total', 2);
    }

    public function test_thread_endpoint_renders_replies_inside_their_parent(): void
    {
        $article = $this->publishedArticle();
        $root = $this->approvedComment($article, ['content' => 'Root comment']);
        $reply = $this->approvedComment($article, [
            'parent_id' => $root->id,
            'content' => 'Nested reply body',
        ]);

        $data = $this->getJson('/comments/' . $article->id . '/thread')->assertStatus(200)->json();

        $this->assertSame(2, $data['total']);
        $this->assertStringContainsString('Root comment', $data['html']);
        $this->assertStringContainsString('Nested reply body', $data['html']);

        // The reply must live inside the parent's replies container.
        $this->assertMatchesRegularExpression(
            '/Root comment.*bh-comment__replies.*Nested reply body/s',
            $data['html']
        );

        $this->assertSame(1, $reply->fresh()->depth());
    }

    public function test_thread_endpoint_excludes_pending_comments(): void
    {
        $article = $this->publishedArticle();
        $this->approvedComment($article, ['content' => 'Visible one']);
        $this->approvedComment($article, ['content' => 'Hidden one', 'status' => 'pending']);

        $data = $this->getJson('/comments/' . $article->id . '/thread')->assertStatus(200)->json();

        $this->assertStringContainsString('Visible one', $data['html']);
        $this->assertStringNotContainsString('Hidden one', $data['html']);
        $this->assertSame(1, $data['total']);
    }

    public function test_blog_detail_page_shows_the_comment_thread(): void
    {
        $article = $this->publishedArticle();
        $root = $this->approvedComment($article, ['content' => 'Top level body']);
        $this->approvedComment($article, ['parent_id' => $root->id, 'content' => 'Second level body']);

        $this->get('/blogs/' . $article->slug)
            ->assertStatus(200)
            ->assertSee('Top level body')
            ->assertSee('Second level body')
            ->assertSee('id="commentsSection"', false);
    }

    // ==================== LIKES ====================

    public function test_like_button_reflects_the_viewers_existing_like(): void
    {
        $viewer = User::factory()->create();
        $article = $this->publishedArticle();
        $comment = $this->approvedComment($article);

        $viewer->commentLikes()->attach($comment->id);
        $comment->update(['likes' => 1]);

        $this->actingAs($viewer)
            ->get('/blogs/' . $article->slug)
            ->assertStatus(200)
            ->assertSee('bh-comment-like is-liked', false);
    }

    public function test_like_state_is_not_shown_to_guests(): void
    {
        $article = $this->publishedArticle();
        $this->approvedComment($article);

        $this->get('/blogs/' . $article->slug)
            ->assertStatus(200)
            ->assertDontSee('bh-comment-like is-liked', false);
    }

    public function test_toggling_a_like_does_not_go_negative(): void
    {
        $user = User::factory()->create();
        $comment = $this->approvedComment(Article::factory()->create(['status' => 'published']), ['likes' => 0]);

        $this->actingAs($user);

        $this->postJson("/comments/{$comment->id}/like")->assertJson(['liked' => true]);
        $this->postJson("/comments/{$comment->id}/like")->assertJson(['liked' => false, 'likes' => 0]);
    }

    // ==================== DELETING SUBTREES ====================

    public function test_deleting_a_comment_removes_all_of_its_replies(): void
    {
        $owner = User::factory()->create();
        $article = $this->publishedArticle();
        $root = $this->approvedComment($article, ['user_id' => $owner->id]);
        $child = $this->approvedComment($article, ['parent_id' => $root->id]);
        $grandchild = $this->approvedComment($article, ['parent_id' => $child->id]);

        $this->actingAs($owner);

        $this->deleteJson("/comments/{$root->id}")
            ->assertStatus(200)
            ->assertJson(['success' => true, 'removed' => 3]);

        $this->assertDatabaseMissing('comments', ['id' => $root->id]);
        $this->assertDatabaseMissing('comments', ['id' => $child->id]);
        $this->assertDatabaseMissing('comments', ['id' => $grandchild->id]);
    }

    public function test_deleting_a_comment_decrements_the_article_count_by_the_whole_subtree(): void
    {
        $owner = User::factory()->create();
        $article = $this->publishedArticle();
        $root = $this->approvedComment($article, ['user_id' => $owner->id]);
        $this->approvedComment($article, ['parent_id' => $root->id]);
        $this->approvedComment($article, ['parent_id' => $root->id]);

        $article->update(['comment_count' => 3]);

        $this->actingAs($owner)->deleteJson("/comments/{$root->id}")->assertStatus(200);

        $this->assertSame(0, $article->fresh()->comment_count);
    }

    public function test_post_owner_can_delete_a_comment_on_their_own_post(): void
    {
        $postOwner = User::factory()->create();
        $commenter = User::factory()->create();
        $article = $this->publishedArticle(['user_id' => $postOwner->id]);
        $comment = $this->approvedComment($article, ['user_id' => $commenter->id]);

        $this->actingAs($postOwner)
            ->deleteJson("/comments/{$comment->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_stranger_still_cannot_delete_someone_elses_comment(): void
    {
        $article = $this->publishedArticle();
        $comment = $this->approvedComment($article, ['user_id' => User::factory()->create()->id]);

        $this->actingAs(User::factory()->create())
            ->deleteJson("/comments/{$comment->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    // ==================== MODERATION ====================

    public function test_moderation_surfaces_the_thread_level_and_reply_count(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = $this->publishedArticle(['user_id' => $admin->id]);
        $root = $this->approvedComment($article);
        $this->approvedComment($article, ['parent_id' => $root->id]);
        $this->approvedComment($article, ['parent_id' => $root->id]);

        $this->actingAs($admin)
            ->get('/admin/comments')
            ->assertStatus(200)
            ->assertSee('Top level')
            ->assertSee('2 replies')
            ->assertSee('Level 1');
    }

    public function test_admin_deleting_a_comment_removes_its_replies_and_reports_the_count(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = $this->publishedArticle(['user_id' => $admin->id]);
        $root = $this->approvedComment($article);
        $this->approvedComment($article, ['parent_id' => $root->id]);
        $article->update(['comment_count' => 2]);

        $this->actingAs($admin)
            ->from('/admin/comments')
            ->post("/admin/comments/{$root->id}/delete")
            ->assertRedirect('/admin/comments')
            ->assertSessionHas('success', 'Comment and 1 reply deleted.');

        $this->assertSame(0, Comment::where('article_id', $article->id)->count());
        $this->assertSame(0, $article->fresh()->comment_count);
    }

    public function test_author_can_moderate_threads_on_their_own_posts(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $article = $this->publishedArticle(['user_id' => $author->id]);
        $root = $this->approvedComment($article);
        $this->approvedComment($article, ['parent_id' => $root->id]);

        $this->actingAs($author)
            ->get('/dashboard/comments')
            ->assertStatus(200)
            ->assertSee('Top level')
            ->assertSee('1 reply');

        $this->actingAs($author)
            ->from('/dashboard/comments')
            ->delete("/dashboard/comments/{$root->id}")
            ->assertRedirect('/dashboard/comments');

        $this->assertSame(0, Comment::where('article_id', $article->id)->count());
    }

    // ==================== MODEL HELPERS ====================

    public function test_descendant_ids_returns_the_full_subtree(): void
    {
        $article = $this->publishedArticle();
        $root = $this->approvedComment($article);
        $child = $this->approvedComment($article, ['parent_id' => $root->id]);
        $grandchild = $this->approvedComment($article, ['parent_id' => $child->id]);

        $ids = $root->descendantIds();

        $this->assertCount(2, $ids);
        $this->assertContains($child->id, $ids);
        $this->assertContains($grandchild->id, $ids);
    }

    public function test_comment_relations_are_wired(): void
    {
        $article = $this->publishedArticle();
        $root = $this->approvedComment($article);
        $reply = $this->approvedComment($article, ['parent_id' => $root->id]);

        $this->assertTrue($root->replies->contains($reply));
        $this->assertTrue($reply->parent->is($root));
        $this->assertTrue($root->isApproved());
    }
}