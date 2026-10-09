<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowAndLiveFeedTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(array $attrs = []): Article
    {
        return Article::factory()->create(array_merge([
            'status' => 'published',
            'published_at' => now(),
        ], $attrs));
    }

    // ==================== FOLLOW: RESPONSE CONTRACT ====================

    /**
     * The follow buttons gated their repaint on `data.success`, but the
     * endpoint never sent it, so the click looked like it had done nothing.
     */
    public function test_follow_endpoint_returns_a_success_flag(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author']);

        $this->actingAs($user)
            ->postJson("/authors/{$author->id}/follow")
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('following', true);
    }

    public function test_unfollow_endpoint_returns_a_success_flag(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author']);

        $user->follows()->attach($author->id);

        $this->actingAs($user)
            ->postJson("/authors/{$author->id}/follow")
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('following', false);
    }

    public function test_follow_endpoint_reports_the_follower_count(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author']);

        $this->actingAs($user)
            ->postJson("/authors/{$author->id}/follow")
            ->assertJsonPath('followers', 1);
    }

    public function test_like_and_bookmark_endpoints_also_return_success(): void
    {
        $user = User::factory()->create();
        $article = $this->publishedArticle();

        $this->actingAs($user)
            ->postJson("/articles/{$article->id}/like")
            ->assertJsonPath('success', true);

        $this->actingAs($user)
            ->postJson("/articles/{$article->id}/bookmark")
            ->assertJsonPath('success', true);
    }

    public function test_guest_cannot_follow(): void
    {
        $author = User::factory()->create(['role' => 'author']);

        $this->postJson("/authors/{$author->id}/follow")->assertStatus(401);
    }

    public function test_cannot_follow_yourself(): void
    {
        $user = User::factory()->create(['role' => 'author']);

        $this->actingAs($user)
            ->postJson("/authors/{$user->id}/follow")
            ->assertStatus(400);
    }

    public function test_following_actually_persists(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author']);

        $this->actingAs($user)->postJson("/authors/{$author->id}/follow")->assertStatus(200);

        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'author_id' => $author->id,
        ]);
    }

    // ==================== FOLLOW: BUTTON MARKUP ====================

    public function test_follow_button_reflects_the_existing_follow_state(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create(['role' => 'author', 'username' => 'follow-target']);

        // Not following yet
        $this->actingAs($viewer)
            ->get('/authors/' . $author->username)
            ->assertStatus(200)
            ->assertSee('data-following="0"', false)
            ->assertSee('data-author-id="' . $author->id . '"', false);

        // Now follow them
        $viewer->follows()->attach($author->id);

        $this->actingAs($viewer)
            ->get('/authors/' . $author->username)
            ->assertStatus(200)
            ->assertSee('data-following="1"', false)
            ->assertSee('Following', false);
    }

    public function test_authors_page_marks_already_followed_authors(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create(['role' => 'author']);

        $viewer->follows()->attach($author->id);

        $html = $this->actingAs($viewer)->get('/authors')->assertStatus(200)->getContent();

        // Attributes sit on separate lines, so match the button block itself
        $this->assertMatchesRegularExpression(
            '/<button[^>]*class="[^"]*follow-btn[^"]*"[^>]*>' . '/s',
            $html
        );
        $this->assertStringContainsString('data-following="1"', $html);
        $this->assertStringContainsString('data-author-id="' . $author->id . '"', $html);
    }

    public function test_follow_javascript_is_shared_not_inlined(): void
    {
        $author = User::factory()->create(['role' => 'author', 'username' => 'shared-js-check']);

        foreach (['/authors', '/authors/' . $author->username] as $path) {
            $html = $this->get($path)->assertStatus(200)->getContent();

            $this->assertStringContainsString('assets/js/follow-actions.js', $html);
            // The old inline handlers gated on a key the API never returned
            $this->assertStringNotContainsString('if (data.success)', $html);
        }
    }

    public function test_follow_module_talks_to_the_right_endpoint(): void
    {
        $path = public_path('assets/js/follow-actions.js');

        $this->assertFileExists($path);

        $source = file_get_contents($path);

        $this->assertStringContainsString("'/authors/' + authorId + '/follow'", $source);
        $this->assertStringContainsString('dataset.following', $source);
        $this->assertStringContainsString("closest('.follow-btn')", $source);
        $this->assertStringContainsString('bloghub:follow-change', $source);
    }

    // ==================== FOLLOW: DASHBOARD LIST ====================

    public function test_followed_author_shows_up_in_the_favourites_following_tab(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author', 'name' => 'Waqas Ahmed']);

        $user->follows()->attach($author->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=following')
            ->assertStatus(200)
            ->assertSee('Waqas Ahmed')
            ->assertSee('Unfollow');
    }

    public function test_following_tab_is_scoped_to_the_viewer(): void
    {
        $user = User::factory()->create();
        $stranger = User::factory()->create();
        $author = User::factory()->create(['role' => 'author', 'name' => 'Someone Else']);

        $stranger->follows()->attach($author->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=following')
            ->assertStatus(200)
            ->assertDontSee('Someone Else');
    }

    public function test_following_after_following_shows_the_author(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['role' => 'author', 'name' => 'Just Followed']);

        $this->actingAs($user)->postJson("/authors/{$author->id}/follow")->assertStatus(200);

        $this->actingAs($user)
            ->get('/favorites?tab=following')
            ->assertStatus(200)
            ->assertSee('Just Followed');
    }

    // ==================== LIVE FEED ENDPOINT ====================

    public function test_feed_returns_articles_published_after_the_cursor(): void
    {
        $older = $this->publishedArticle();
        $newer = $this->publishedArticle();

        $this->getJson('/api/posts/feed?after=' . $older->id)
            ->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('cursor', $newer->id)
            ->assertJsonPath('articles.0.id', $newer->id);
    }

    public function test_feed_is_empty_when_nothing_is_new(): void
    {
        $article = $this->publishedArticle();

        $this->getJson('/api/posts/feed?after=' . $article->id)
            ->assertStatus(200)
            ->assertJsonPath('count', 0)
            ->assertJsonPath('html', '');
    }

    public function test_feed_never_returns_drafts_or_pending_posts(): void
    {
        Article::factory()->create(['status' => 'draft', 'published_at' => now()]);
        Article::factory()->create(['status' => 'pending', 'published_at' => now()]);

        $this->getJson('/api/posts/feed')
            ->assertStatus(200)
            ->assertJsonPath('count', 0);
    }

    public function test_feed_includes_card_html_with_grid_and_id_marker(): void
    {
        $article = $this->publishedArticle();

        $data = $this->getJson('/api/posts/feed')->assertStatus(200)->json();

        $this->assertStringContainsString('data-article-id="' . $article->id . '"', $data['html']);
        // The grid column class must survive so the layout stays intact
        $this->assertStringContainsString('col-md-6 col-lg-4', $data['html']);
    }

    public function test_feed_reports_the_author_and_url(): void
    {
        $author = User::factory()->create(['role' => 'author', 'name' => 'Feed Author']);
        $this->publishedArticle(['user_id' => $author->id]);

        $this->getJson('/api/posts/feed')
            ->assertStatus(200)
            ->assertJsonPath('articles.0.author', 'Feed Author')
            ->assertJsonStructure(['articles' => [['id', 'title', 'url', 'author', 'published_at']]]);
    }

    public function test_feed_caps_the_number_of_returned_articles(): void
    {
        Article::factory()->count(15)->create(['status' => 'published', 'published_at' => now()]);

        $this->getJson('/api/posts/feed')
            ->assertStatus(200)
            ->assertJsonPath('count', 10);
    }

    public function test_feed_works_for_guests(): void
    {
        $this->publishedArticle();

        $this->getJson('/api/posts/feed')->assertStatus(200);
    }

    // ==================== LIVE FEED WIRING ====================

    public function test_blogs_page_wires_up_the_live_feed(): void
    {
        $html = $this->get('/blogs')->assertStatus(200)->getContent();

        $this->assertStringContainsString('data-live-feed', $html);
        $this->assertStringContainsString('data-live-cursor=', $html);
        $this->assertStringContainsString('assets/js/live-feed.js', $html);
    }

    public function test_blogs_page_cursor_matches_the_newest_rendered_post(): void
    {
        $newest = $this->publishedArticle();

        $html = $this->get('/blogs')->assertStatus(200)->getContent();

        preg_match('/data-live-cursor="(\d+)"/', $html, $m);

        $this->assertNotEmpty($m);
        $this->assertSame($newest->id, (int) $m[1]);
    }

    public function test_live_feed_script_is_served(): void
    {
        $path = public_path('assets/js/live-feed.js');

        $this->assertFileExists($path);

        $source = file_get_contents($path);
        $this->assertStringContainsString('/api/posts/feed', $source);
        $this->assertStringContainsString('data-live-cursor', $source);
    }

    // ==================== PUBLISH TIMESTAMPS ====================

    /**
     * published_at was a DATE column, so the time was discarded on write and a
     * post published at 10:05 rendered as "10 hrs ago".
     */
    public function test_publish_time_is_preserved_to_the_minute(): void
    {
        $article = $this->publishedArticle(['published_at' => now()]);

        $fresh = Article::find($article->id);

        $this->assertSame(
            now()->format('H:i'),
            $fresh->published_at->format('H:i'),
            'published_at must keep its time component, not truncate to midnight.'
        );
    }

    public function test_published_at_is_cast_to_datetime(): void
    {
        $article = $this->publishedArticle(['published_at' => now()]);

        $this->assertSame(
            now()->format('Y-m-d H:i'),
            Article::find($article->id)->published_at->format('Y-m-d H:i')
        );
    }

    public function test_recently_published_post_reads_as_just_now(): void
    {
        $article = $this->publishedArticle(['published_at' => now()]);

        $this->assertSame(
            'just now',
            \App\Support\RelativeTime::format(Article::find($article->id)->published_at->timestamp)
        );
    }

    public function test_no_post_is_dated_in_the_future(): void
    {
        $this->publishedArticle(['published_at' => now()]);

        $this->assertSame(0, Article::where('published_at', '>', now())->count());
    }
}