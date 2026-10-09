<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(array $attrs = []): Article
    {
        return Article::factory()->create(array_merge(['status' => 'published'], $attrs));
    }

    // ==================== ACCESS ====================

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/favorites')->assertRedirect('/login');
    }

    public function test_reader_can_open_the_favourites_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'reader']))
            ->get('/favorites')
            ->assertStatus(200)
            ->assertSee('My Favourites', false);
    }

    public function test_author_can_open_the_favourites_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'author']))
            ->get('/favorites')
            ->assertStatus(200);
    }

    public function test_admin_can_open_the_favourites_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/favorites')
            ->assertStatus(200);
    }

    public function test_empty_page_shows_the_posts_empty_state_by_default(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/favorites')
            ->assertStatus(200)
            ->assertSee('No favourited posts yet');
    }

    // ==================== TABS ====================

    public function test_all_tabs_are_reachable(): void
    {
        $user = User::factory()->create();

        foreach (['posts', 'comments', 'bookmarks', 'following'] as $tab) {
            $this->actingAs($user)
                ->get('/favorites?tab=' . $tab)
                ->assertStatus(200)
                ->assertSee('tab=' . $tab, false);
        }
    }

    public function test_unknown_tab_falls_back_to_posts(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/favorites?tab=bogus')
            ->assertStatus(200)
            ->assertSee('No favourited posts yet');
    }

    // ==================== FAVOURITED POSTS ====================

    public function test_favourited_posts_are_listed(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle(['title' => 'A Favourite Article']);

        $user->articleLikes()->attach($article->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=posts')
            ->assertStatus(200)
            ->assertSee('A Favourite Article');
    }

    public function test_unpublished_articles_are_hidden_from_favourites(): void
    {
        $user = User::factory()->create();
        $draft = Article::factory()->create(['status' => 'draft', 'title' => 'Secret Draft']);

        $user->articleLikes()->attach($draft->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=posts')
            ->assertStatus(200)
            ->assertDontSee('Secret Draft');
    }

    // ==================== LIKED COMMENTS ====================

    public function test_liked_comments_are_listed_with_their_article(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle(['title' => 'Threaded Post']);
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'approved',
            'content' => 'A memorable comment',
        ]);

        $user->commentLikes()->attach($comment->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=comments')
            ->assertStatus(200)
            ->assertSee('A memorable comment')
            ->assertSee('Threaded Post');
    }

    public function test_pending_comments_are_hidden_from_liked_comments(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'pending',
            'content' => 'Hidden pending comment',
        ]);

        $user->commentLikes()->attach($comment->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=comments')
            ->assertStatus(200)
            ->assertDontSee('Hidden pending comment');
    }

    public function test_liked_replies_show_who_they_reply_to(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $parentAuthor = User::factory()->create(['name' => 'ParentPerson']);

        $parent = Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'approved',
            'user_id' => $parentAuthor->id,
        ]);

        $reply = Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'approved',
            'parent_id' => $parent->id,
            'content' => 'A nested liked reply',
        ]);

        $user->commentLikes()->attach($reply->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=comments')
            ->assertStatus(200)
            ->assertSee('A nested liked reply')
            ->assertSee('ParentPerson');
    }

    // ==================== BOOKMARKS ====================

    public function test_bookmarks_are_listed(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle(['title' => 'Saved For Later']);

        $user->bookmarks()->attach($article->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=bookmarks')
            ->assertStatus(200)
            ->assertSee('Saved For Later');
    }

    // ==================== FOLLOWING ====================

    public function test_followed_authors_are_listed(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create([
            'role' => 'author',
            'name' => 'FollowedAuthor',
            'tagline' => 'Writes about testing',
        ]);

        $user->follows()->attach($author->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=following')
            ->assertStatus(200)
            ->assertSee('FollowedAuthor')
            ->assertSee('Writes about testing');
    }

    // ==================== COUNTS ====================

    public function test_tab_badges_reflect_the_real_counts(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author']);
        $article = $this->publishedArticle();
        $comment = Comment::factory()->create(['article_id' => $article->id, 'status' => 'approved']);

        $user->articleLikes()->attach($article->id, ['created_at' => now(), 'updated_at' => now()]);
        $user->commentLikes()->attach($comment->id, ['created_at' => now(), 'updated_at' => now()]);
        $user->bookmarks()->attach($article->id, ['created_at' => now(), 'updated_at' => now()]);
        $user->follows()->attach($author->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=posts')
            ->assertStatus(200)
            // One post + one comment + one bookmark + one followed author
            ->assertSee('4 items saved');
    }

    public function test_library_is_scoped_to_the_signed_in_user(): void
    {
        $user = User::factory()->create();
        $stranger = User::factory()->create();
        $article = $this->publishedArticle(['title' => 'Someone Elses Favourite']);

        $stranger->articleLikes()->attach($article->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=posts')
            ->assertStatus(200)
            ->assertDontSee('Someone Elses Favourite')
            ->assertSee('No favourited posts yet');
    }

    // ==================== REMOVAL ENDPOINTS ====================

    public function test_unliking_a_post_works_from_the_favourites_page(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();

        $user->articleLikes()->attach($article->id);

        $this->actingAs($user)
            ->postJson("/articles/{$article->id}/like")
            ->assertStatus(200)
            ->assertJsonPath('liked', false);

        $this->assertDatabaseMissing('article_likes', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_unliking_a_comment_works_from_the_favourites_page(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $comment = Comment::factory()->create(['article_id' => $article->id, 'status' => 'approved']);

        $user->commentLikes()->attach($comment->id);

        $this->actingAs($user)
            ->postJson("/comments/{$comment->id}/like")
            ->assertStatus(200)
            ->assertJsonPath('liked', false);

        $this->assertDatabaseMissing('comment_likes', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
        ]);
    }

    public function test_unfollowing_from_the_favourites_page(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author']);

        $user->follows()->attach($author->id);

        $this->actingAs($user)
            ->postJson("/authors/{$author->id}/follow")
            ->assertStatus(200)
            ->assertJsonPath('following', false);
    }

    // ==================== NAVIGATION ====================

    public function test_blade_directives_never_leak_into_the_rendered_page(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();
        $user->articleLikes()->attach($article->id, ['created_at' => now(), 'updated_at' => now()]);

        foreach (['posts', 'comments', 'bookmarks', 'following'] as $tab) {
            $html = $this->actingAs($user)->get('/favorites?tab=' . $tab)->assertStatus(200)->getContent();

            foreach (['@if', '@else', '@endif', '@foreach', '@php'] as $directive) {
                $this->assertStringNotContainsString(
                    $directive,
                    strip_tags($html),
                    "Uncompiled Blade directive [{$directive}] leaked into the [{$tab}] tab."
                );
            }
        }
    }

    public function test_empty_library_shows_a_prompt_instead_of_a_count(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/favorites')
            ->assertStatus(200)
            ->assertSee('Nothing saved yet');
    }

    public function test_favourites_link_is_visible_in_the_navbar_dropdown(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertStatus(200)
            ->assertSee(route('favorites.index'), false);
    }
}