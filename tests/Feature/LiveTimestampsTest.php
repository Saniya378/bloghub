<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Support\RelativeTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveTimestampsTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(array $attrs = []): Article
    {
        return Article::factory()->create(array_merge(['status' => 'published'], $attrs));
    }

    // ==================== FORMATTING RULES ====================

    public function test_anything_under_five_seconds_reads_as_just_now(): void
    {
        $now = 1_700_000_000;

        foreach ([0, 1, 2, 3, 4] as $ago) {
            $this->assertSame('just now', RelativeTime::format($now - $ago, $now));
        }
    }

    public function test_seconds_are_counted_precisely(): void
    {
        $now = 1_700_000_000;

        $this->assertSame('5 secs ago', RelativeTime::format($now - 5, $now));
        $this->assertSame('9 secs ago', RelativeTime::format($now - 9, $now));
        $this->assertSame('59 secs ago', RelativeTime::format($now - 59, $now));
    }

    public function test_minutes_hours_days_weeks_months_and_years(): void
    {
        $now = 1_700_000_000;

        $this->assertSame('1 min ago', RelativeTime::format($now - 60, $now));
        $this->assertSame('5 mins ago', RelativeTime::format($now - 300, $now));
        $this->assertSame('59 mins ago', RelativeTime::format($now - 59 * 60, $now));

        $this->assertSame('1 hr ago', RelativeTime::format($now - 3600, $now));
        $this->assertSame('23 hrs ago', RelativeTime::format($now - 23 * 3600, $now));

        $this->assertSame('1 day ago', RelativeTime::format($now - 86400, $now));
        $this->assertSame('6 days ago', RelativeTime::format($now - 6 * 86400, $now));

        $this->assertSame('1 wk ago', RelativeTime::format($now - 7 * 86400, $now));
        $this->assertSame('4 wks ago', RelativeTime::format($now - 30 * 86400, $now));

        $this->assertSame('1 mo ago', RelativeTime::format($now - 35 * 86400, $now));
        $this->assertSame('11 mos ago', RelativeTime::format($now - 350 * 86400, $now));

        $this->assertSame('1 yr ago', RelativeTime::format($now - 365 * 86400, $now));
        $this->assertSame('3 yrs ago', RelativeTime::format($now - 3 * 365 * 86400, $now));
    }

    public function test_future_timestamps_never_render_negative_ages(): void
    {
        $now = 1_700_000_000;

        // Visitor clock slightly ahead of the server must not show "-5 secs ago".
        $this->assertSame('just now', RelativeTime::format($now + 5, $now));
        $this->assertSame('just now', RelativeTime::format($now + 3600, $now));
    }

    // ==================== MARKUP CONTRACT ====================

    public function test_time_ago_renders_a_timestamp_the_javascript_can_read(): void
    {
        // Freeze the clock so the rendered label cannot drift between the
        // insert and the assertion.
        $this->travelTo(now()->startOfSecond());

        $article = $this->publishedArticle();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'approved',
            'created_at' => now()->subSeconds(30),
            'updated_at' => now()->subSeconds(30),
        ]);

        $response = $this->get('/blogs/' . $article->slug)->assertStatus(200);

        $response->assertSee('bh-time-ago', false)
            ->assertSee('data-timestamp="' . $comment->created_at->timestamp . '"', false)
            ->assertSee('30 secs ago');

        $this->travelBack();
    }

    public function test_a_brand_new_comment_renders_as_just_now(): void
    {
        $this->travelTo(now()->startOfSecond());

        $article = $this->publishedArticle();
        Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/blogs/' . $article->slug)
            ->assertStatus(200)
            ->assertSee('just now');

        $this->travelBack();
    }

    public function test_each_second_bucket_has_its_own_label(): void
    {
        $this->travelTo(now()->startOfSecond());

        $article = $this->publishedArticle();

        foreach ([2 => 'just now', 7 => '7 secs ago', 45 => '45 secs ago'] as $ago => $expected) {
            Comment::factory()->create([
                'article_id' => $article->id,
                'status' => 'approved',
                'content' => "Comment posted {$ago} seconds ago",
                'created_at' => now()->subSeconds($ago),
                'updated_at' => now()->subSeconds($ago),
            ]);
        }

        $this->get('/blogs/' . $article->slug)
            ->assertStatus(200)
            ->assertSee('just now')
            ->assertSee('7 secs ago')
            ->assertSee('45 secs ago');

        $this->travelBack();
    }

    public function test_prefixed_labels_keep_their_static_part(): void
    {
        $this->travelTo(now()->startOfSecond());

        $article = $this->publishedArticle();
        $like = now()->subMinutes(3);

        $user = User::factory()->create();
        $user->articleLikes()->attach($article->id, ['created_at' => $like, 'updated_at' => now()]);

        $this->actingAs($user)
            ->get('/favorites?tab=posts')
            ->assertStatus(200)
            ->assertSee('Liked 3 mins ago');

        $this->travelBack();
    }

    public function test_semantic_time_element_carries_an_iso_date_and_a_tooltip(): void
    {
        $article = $this->publishedArticle();
        Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'approved',
        ]);

        $html = $this->get('/blogs/' . $article->slug)->assertStatus(200)->getContent();

        $this->assertMatchesRegularExpression(
            '/<time\s+class="bh-time-ago[^"]*"\s+datetime="[^"]+"\s+data-timestamp="\d+"\s+title="[^"]+"\s*>/',
            $html
        );
    }

    // ==================== SCRIPT WIRING ====================

    public function test_public_pages_load_the_live_timestamp_script(): void
    {
        $article = $this->publishedArticle();

        $html = $this->get('/blogs/' . $article->slug)->assertStatus(200)->getContent();

        $this->assertStringContainsString('assets/js/relative-time.js', $html);
        // Client clock drift is corrected against the server clock.
        $this->assertStringContainsString('window.blogHubServerTime', $html);
    }

    public function test_dashboard_pages_load_the_live_timestamp_script(): void
    {
        $author = User::factory()->create(['role' => 'author']);

        $html = $this->actingAs($author)
            ->get('/dashboard/comments')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('assets/js/relative-time.js', $html);
        $this->assertStringContainsString('window.blogHubServerTime', $html);
    }

    public function test_thread_endpoint_also_ships_ticking_timestamps(): void
    {
        $this->travelTo(now()->startOfSecond());

        $article = $this->publishedArticle();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'status' => 'approved',
            'created_at' => now()->subSeconds(12),
            'updated_at' => now()->subSeconds(12),
        ]);

        $data = $this->getJson('/comments/' . $article->id . '/thread')
            ->assertStatus(200)
            ->json();

        $this->assertStringContainsString('data-timestamp="' . $comment->created_at->timestamp . '"', $data['html']);
        $this->assertStringContainsString('12 secs ago', $data['html']);

        $this->travelBack();
    }

    // ==================== NO STATIC RELATIVE STRINGS LEFT ====================

    public function test_no_view_still_renders_a_frozen_relative_time(): void
    {
        $views = [
            'pages.blog-details',
            'pages.favorites',
            'pages.dashboard.comments',
            'pages.admin.comments',
            'pages.admin.messages',
            'pages.admin.posts',
            'pages.dashboard.posts',
            'pages.dashboard.post-form',
            'pages.dashboard.notifications',
            'pages.search-results',
            'pages.author-profile',
            'components.article-card',
            'components.comment-item',
        ];

        foreach ($views as $view) {
            $path = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');

            $this->assertFileExists($path);
            $this->assertStringNotContainsString(
                'diffForHumans',
                file_get_contents($path),
                "{$view} still renders a static diffForHumans() label."
            );
        }
    }
}