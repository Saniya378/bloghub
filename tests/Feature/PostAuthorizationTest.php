<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // ==================== ARTICLE CREATION TESTS ====================

    public function test_authenticated_user_can_create_article(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/articles', [
            'title' => 'Test Article',
            'excerpt' => 'Test excerpt',
            'content' => '<p>Test content</p>',
            'category_slug' => 'technology',
            'tags' => ['test', 'php'],
            'status' => 'pending',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_publish_directly_on_creation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Publishing is only possible through admin approval
        $response = $this->postJson('/articles', [
            'title' => 'Direct Publish Attempt',
            'excerpt' => 'Test excerpt',
            'content' => '<p>Test content</p>',
            'category_slug' => 'technology',
            'status' => 'published',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('articles', ['title' => 'Direct Publish Attempt']);
    }

    public function test_guest_cannot_create_article(): void
    {
        $response = $this->post('/articles', [
            'title' => 'Test Article',
            'excerpt' => 'Test excerpt',
            'content' => '<p>Test content</p>',
            'category_slug' => 'technology',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('articles', ['title' => 'Test Article']);
    }

    public function test_article_creation_requires_title(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/articles', [
            'title' => '',
            'excerpt' => 'Test excerpt',
            'content' => '<p>Test content</p>',
            'category_slug' => 'technology',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_article_creation_requires_content(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/articles', [
            'title' => 'Test Article',
            'excerpt' => 'Test excerpt',
            'content' => '',
            'category_slug' => 'technology',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_article_creation_requires_valid_category(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/articles', [
            'title' => 'Test Article',
            'excerpt' => 'Test excerpt',
            'content' => '<p>Test content</p>',
            'category_slug' => 'nonexistent-category',
        ]);

        $response->assertSessionHasErrors('category_slug');
    }

    // ==================== ARTICLE UPDATE TESTS ====================

    public function test_user_can_update_own_article(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
        ]);

        $this->actingAs($user);

        $response = $this->put("/articles/{$article->id}", [
            'title' => 'Updated Title',
            'excerpt' => 'Updated excerpt',
            'content' => '<p>Updated content</p>',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_user_cannot_update_another_users_article(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $owner->id,
            'title' => 'Original Title',
        ]);

        $this->actingAs($otherUser);

        $response = $this->put("/articles/{$article->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Original Title',
        ]);
    }

    public function test_admin_can_update_any_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $owner->id,
            'title' => 'Original Title',
        ]);

        $this->actingAs($admin);

        $response = $this->put("/articles/{$article->id}", [
            'title' => 'Admin Updated Title',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Admin Updated Title',
        ]);
    }

    public function test_guest_cannot_update_article(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->put("/articles/{$article->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertRedirect('/login');
    }

    // ==================== ARTICLE DELETION TESTS ====================

    public function test_user_can_delete_own_article(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/articles/{$article->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_user_cannot_delete_another_users_article(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->actingAs($otherUser);

        $response = $this->delete("/articles/{$article->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }

    public function test_admin_can_delete_any_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->actingAs($admin);

        $response = $this->delete("/articles/{$article->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_guest_cannot_delete_article(): void
    {
        $user = User::factory()->create();
        $article = \App\Models\Article::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->delete("/articles/{$article->id}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }

    // ==================== ARTICLE STATUS TESTS ====================

    public function test_draft_articles_not_visible_to_public(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
            'title' => 'Draft Article',
        ]);

        $response = $this->get('/blogs');
        $response->assertStatus(200);
        $response->assertDontSee('Draft Article');
    }

    public function test_draft_articles_visible_to_owner(): void
    {
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
            'title' => 'Draft Article',
        ]);

        $this->actingAs($user);

        $response = $this->get('/blogs');
        $response->assertStatus(200);
        $response->assertSee('Draft Article');
    }

    public function test_draft_articles_visible_to_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        \App\Models\Article::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
            'title' => 'Draft Article',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/blogs');
        $response->assertStatus(200);
        $response->assertSee('Draft Article');
    }

    // ==================== MASS ASSIGNMENT PROTECTION ====================

    public function test_user_cannot_mass_assign_role_on_creation(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'admin', // Attempt to mass assign
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'user', // Should be 'user', not 'admin'
        ]);
    }

    public function test_user_cannot_mass_assign_is_active_on_creation(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'is_active' => false, // Attempt to deactivate own account
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'is_active' => true, // Should be true
        ]);
    }
}
