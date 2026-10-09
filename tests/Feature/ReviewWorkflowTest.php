<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    // ==================== AUTHOR SIDE ====================

    public function test_author_submitting_post_creates_pending_article(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author);

        $response = $this->post('/dashboard/posts', [
            'title' => 'Pending Post',
            'slug' => 'pending-post',
            'excerpt' => 'Excerpt',
            'content' => '<p>Content</p>',
            'category_slug' => 'technology',
            'status' => 'pending',
        ]);

        $response->assertRedirect('/dashboard/posts');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('articles', [
            'title' => 'Pending Post',
            'status' => 'pending',
            'user_id' => $author->id,
        ]);

        $this->assertNotNull(Article::where('title', 'Pending Post')->first()->submitted_at);
    }

    public function test_author_cannot_publish_directly_from_dashboard(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author);

        $response = $this->post('/dashboard/posts', [
            'title' => 'Sneaky Publish',
            'slug' => 'sneaky-publish',
            'excerpt' => 'Excerpt',
            'content' => '<p>Content</p>',
            'category_slug' => 'technology',
            'status' => 'published',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseMissing('articles', ['title' => 'Sneaky Publish']);
    }

    public function test_author_cannot_publish_via_json_endpoint(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author);

        $response = $this->postJson('/articles', [
            'title' => 'JSON Publish Attempt',
            'excerpt' => 'Excerpt',
            'content' => '<p>Content</p>',
            'category_slug' => 'technology',
            'status' => 'published',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
        $this->assertDatabaseMissing('articles', ['title' => 'JSON Publish Attempt']);
    }

    public function test_author_cannot_publish_existing_article_via_json_endpoint(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->draft()->create(['user_id' => $author->id]);

        $this->actingAs($author);

        $response = $this->putJson("/articles/{$article->id}", ['status' => 'published']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => 'draft']);
    }

    public function test_pending_post_is_hidden_from_the_public(): void
    {
        Article::factory()->create([
            'title' => 'Hidden Pending Post',
            'status' => 'pending',
            'slug' => 'hidden-pending-post',
        ]);

        $this->get('/blogs')->assertStatus(200)->assertDontSee('Hidden Pending Post');
        $this->get('/blogs/hidden-pending-post')->assertNotFound();
    }

    public function test_author_can_preview_their_pending_post(): void
    {
        $author = User::factory()->author()->create();
        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'My Pending Post',
            'slug' => 'my-pending-post',
            'status' => 'pending',
        ]);

        $this->actingAs($author)->get('/blogs/my-pending-post')->assertStatus(200);
    }

    public function test_saving_a_pending_post_as_draft_withdraws_it_from_review(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Withdraw Me',
            'slug' => 'withdraw-me',
            'status' => 'pending',
        ]);

        $this->actingAs($author)->put("/dashboard/posts/{$article->id}", [
            'title' => 'Withdraw Me',
            'slug' => 'withdraw-me',
            'excerpt' => 'Excerpt',
            'content' => '<p>Content</p>',
            'category_slug' => 'technology',
            'status' => 'draft',
        ])->assertRedirect('/dashboard/posts');

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => 'draft']);
    }

    // ==================== ADMIN SIDE ====================

    public function test_admin_approval_publishes_the_post(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create([
            'title' => 'Approve Me',
            'status' => 'pending',
            'published_at' => null,
        ]);

        $this->actingAs($admin)
            ->post("/admin/posts/{$article->id}/approve")
            ->assertRedirect();

        $article->refresh();

        $this->assertSame('published', $article->status);
        $this->assertNotNull($article->published_at);
        $this->assertSame($admin->id, $article->reviewed_by);
        $this->assertNotNull($article->reviewed_at);
    }

    public function test_admin_rejection_requires_a_note(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->post("/admin/posts/{$article->id}/reject")
            ->assertSessionHasErrors('review_note');

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => 'pending']);
    }

    public function test_admin_rejection_sends_post_back_with_a_note(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->post("/admin/posts/{$article->id}/reject", ['review_note' => 'Please add sources.'])
            ->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'status' => 'rejected',
            'review_note' => 'Please add sources.',
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_author_can_resubmit_a_rejected_post(): void
    {
        $author = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Rejected Post',
            'slug' => 'rejected-post',
            'status' => 'pending',
        ]);
        $article->reject($admin, 'Needs work');

        $this->actingAs($author)->put("/dashboard/posts/{$article->id}", [
            'title' => 'Rejected Post',
            'slug' => 'rejected-post',
            'excerpt' => 'Updated excerpt',
            'content' => '<p>Updated content</p>',
            'category_slug' => 'technology',
            'status' => 'pending',
        ])->assertRedirect('/dashboard/posts');

        $article->refresh();

        $this->assertSame('pending', $article->status);
        $this->assertNull($article->review_note);
        $this->assertNull($article->reviewed_by);
    }

    public function test_non_admin_cannot_approve_or_reject(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->create(['status' => 'pending']);

        // The role middleware blocks non-admins before the controller runs
        $this->actingAs($author)->post("/admin/posts/{$article->id}/approve")->assertRedirect();
        $this->actingAs($author)->post("/admin/posts/{$article->id}/reject", ['review_note' => 'x'])->assertRedirect();

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => 'pending']);
    }

    public function test_approving_a_post_that_is_not_in_review_is_refused(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->published()->create();

        $this->actingAs($admin)
            ->post("/admin/posts/{$article->id}/approve")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => 'published']);
    }

    // ==================== NOTIFICATIONS ====================

    public function test_author_is_notified_when_their_post_is_approved(): void
    {
        $author = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['user_id' => $author->id, 'status' => 'pending']);

        $this->actingAs($admin)->post("/admin/posts/{$article->id}/approve");

        $this->assertSame(1, $author->fresh()->unreadNotifications()->count());

        $notification = $author->fresh()->unreadNotifications()->first();

        $this->assertSame('post_approved', $notification->data['type']);
        $this->assertSame($article->title, $notification->data['title']);
        $this->assertSame($admin->name, $notification->data['admin_name']);
    }

    public function test_author_is_notified_with_the_reason_when_their_post_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['user_id' => $author->id, 'status' => 'pending']);

        $this->actingAs($admin)->post("/admin/posts/{$article->id}/reject", [
            'review_note' => 'Please add sources.',
        ]);

        $notification = $author->fresh()->unreadNotifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('post_rejected', $notification->data['type']);
        $this->assertSame('Please add sources.', $notification->data['note']);
    }

    public function test_admin_gets_notified_when_their_own_post_is_approved(): void
    {
        // Admins can be authors too: the status change is still worth reporting
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['user_id' => $admin->id, 'status' => 'pending']);

        $this->actingAs($admin)->post("/admin/posts/{$article->id}/approve");

        $this->assertSame(1, $admin->fresh()->unreadNotifications()->count());
    }

    public function test_unrelated_users_do_not_receive_the_notification(): void
    {
        $author = User::factory()->author()->create();
        $other = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['user_id' => $author->id, 'status' => 'pending']);

        $this->actingAs($admin)->post("/admin/posts/{$article->id}/approve");

        $this->assertSame(0, $other->fresh()->unreadNotifications()->count());
    }

    public function test_author_can_read_their_notifications_page_and_mark_them_read(): void
    {
        $author = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['user_id' => $author->id, 'status' => 'pending']);

        $this->actingAs($admin)->post("/admin/posts/{$article->id}/approve");

        $response = $this->actingAs($author)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Notifications');
        $response->assertSee($article->title);

        $this->actingAs($author)->post('/notifications/read-all')->assertRedirect();

        $this->assertSame(0, $author->fresh()->unreadNotifications()->count());
    }

    public function test_guest_cannot_see_notifications(): void
    {
        $this->get('/notifications')->assertRedirect('/login');
    }
}