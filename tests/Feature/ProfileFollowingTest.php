<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileFollowingTest extends TestCase
{
    use RefreshDatabase;

    private function author(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'author',
            'username' => 'author-' . \Illuminate\Support\Str::random(6),
            'tagline' => 'Writes about things',
        ], $attrs));
    }

    // ==================== SECTION RENDERS ====================

    public function test_profile_page_has_an_authors_you_follow_section(): void
    {
        $html = $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('Authors You Follow', $html);
        $this->assertStringContainsString('id="followingCount"', $html);
        $this->assertStringContainsString('id="followingCard"', $html);
    }

    public function test_followed_author_is_listed_on_the_profile(): void
    {
        $user = User::factory()->create();
        $author = $this->author(['name' => 'Profile Followed']);

        $user->follows()->attach($author->id, ['created_at' => now(), 'updated_at' => now()]);

        $html = $this->actingAs($user)->get('/profile')->assertStatus(200)->getContent();

        $this->assertStringContainsString('Profile Followed', $html);
        $this->assertStringContainsString('data-following-row="' . $author->id . '"', $html);
        $this->assertStringContainsString('data-author-id="' . $author->id . '"', $html);
        $this->assertStringContainsString('/authors/author-', $html);
    }

    public function test_profile_shows_author_stats(): void
    {
        $user = User::factory()->create();
        $author = $this->author(['name' => 'Stats Author']);

        $user->follows()->attach($author->id);
        \App\Models\Article::factory()->count(3)->create([
            'user_id' => $author->id,
            'status' => 'published',
        ]);

        $html = $this->actingAs($user)->get('/profile')->assertStatus(200)->getContent();

        $this->assertStringContainsString('3 posts', $html);
    }

    public function test_profile_badge_matches_the_follow_count(): void
    {
        $user = User::factory()->create();

        $user->follows()->attach($this->author()->id);
        $user->follows()->attach($this->author()->id);
        $user->follows()->attach($this->author()->id);

        $html = $this->actingAs($user)->get('/profile')->assertStatus(200)->getContent();

        $this->assertMatchesRegularExpression('/id="followingCount">\s*3\s*</', $html);
        $this->assertStringContainsString('data-following-total="3"', $html);
    }

    public function test_profile_is_scoped_to_the_viewer(): void
    {
        $user = User::factory()->create();
        $stranger = User::factory()->create();
        $author = $this->author(['name' => 'Strangers Author']);

        $stranger->follows()->attach($author->id);

        $this->actingAs($user)
            ->get('/profile')
            ->assertStatus(200)
            ->assertDontSee('Strangers Author');
    }

    // ==================== EMPTY STATE ====================

    public function test_profile_shows_an_empty_state_when_following_nobody(): void
    {
        $html = $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('You are not following anyone yet', $html);
        $this->assertStringContainsString('Discover Authors', $html);
        $this->assertStringNotContainsString('data-following-row', $html);
    }

    public function test_profile_shows_no_empty_state_when_following_someone(): void
    {
        $user = User::factory()->create();
        $user->follows()->attach($this->author()->id);

        $this->actingAs($user)
            ->get('/profile')
            ->assertStatus(200)
            ->assertDontSee('You are not following anyone yet');
    }

    // ==================== FOLLOW <-> PROFILE ROUND TRIP ====================

    public function test_following_then_opening_profile_shows_the_author(): void
    {
        $user = User::factory()->create();
        $author = $this->author(['name' => 'Round Trip']);

        $this->actingAs($user)
            ->postJson("/authors/{$author->id}/follow")
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->actingAs($user)
            ->get('/profile')
            ->assertStatus(200)
            ->assertSee('Round Trip');
    }

    public function test_unfollowing_then_opening_profile_removes_the_author(): void
    {
        $user = User::factory()->create();
        $author = $this->author(['name' => 'Gone After Unfollow']);

        $user->follows()->attach($author->id);

        $this->actingAs($user)->get('/profile')->assertStatus(200)->assertSee('Gone After Unfollow');

        $this->actingAs($user)
            ->postJson("/authors/{$author->id}/follow")
            ->assertStatus(200)
            ->assertJsonPath('following', false);

        $this->actingAs($user)
            ->get('/profile')
            ->assertStatus(200)
            ->assertDontSee('Gone After Unfollow');
    }

    // ==================== BUTTON WIRING ====================

    public function test_profile_unfollow_button_is_marked_for_row_removal(): void
    {
        $user = User::factory()->create();
        $author = $this->author();

        $user->follows()->attach($author->id);

        $html = $this->actingAs($user)->get('/profile')->assertStatus(200)->getContent();

        $this->assertStringContainsString('data-remove-row="1"', $html);
        $this->assertStringContainsString('data-following="1"', $html);
        $this->assertStringContainsString('aria-pressed="true"', $html);
    }

    public function test_profile_loads_the_shared_follow_module(): void
    {
        $html = $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('assets/js/follow-actions.js', $html);
        $this->assertStringContainsString('bloghub:follow-change', $html);
    }

    public function test_follow_module_supports_row_removal(): void
    {
        $source = file_get_contents(public_path('assets/js/follow-actions.js'));

        $this->assertStringContainsString("dataset.removeRow", $source);
        $this->assertStringContainsString('data-following-row', $source);
    }

    public function test_manage_all_link_points_at_the_following_tab(): void
    {
        $user = User::factory()->create();
        $user->follows()->attach($this->author()->id);

        $html = $this->actingAs($user)->get('/profile')->assertStatus(200)->getContent();

        $this->assertStringContainsString(route('favorites.index', ['tab' => 'following']), $html);
    }

    // ==================== PAGINATION ====================

    public function test_profile_following_list_paginates(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 15; $i++) {
            $user->follows()->attach($this->author()->id);
        }

        $html = $this->actingAs($user)->get('/profile')->assertStatus(200)->getContent();

        $this->assertSame(12, substr_count($html, 'data-following-row='));
        // Laravel's paginator marks a next page with rel="next"
        $this->assertStringContainsString('rel="next"', $html);
    }
}