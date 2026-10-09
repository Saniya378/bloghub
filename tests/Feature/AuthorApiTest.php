<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Tech topics',
            'icon' => 'laptop',
        ]);

        Category::create([
            'name' => 'Travel',
            'slug' => 'travel',
            'description' => 'Travel stories',
            'icon' => 'plane',
        ]);
    }

    public function test_authors_index_listing_and_filtering(): void
    {
        $author1 = User::factory()->create([
            'name' => 'John Developer',
            'username' => 'johndev',
            'role' => 'author',
            'specialty' => 'Backend Development',
            'is_active' => true,
        ]);

        $author2 = User::factory()->create([
            'name' => 'Sarah Designer',
            'username' => 'sarahui',
            'role' => 'author',
            'specialty' => 'UI UX Design',
            'is_active' => true,
        ]);

        // Create published articles
        Article::factory()->count(3)->create([
            'user_id' => $author1->id,
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        Article::factory()->count(1)->create([
            'user_id' => $author2->id,
            'category_slug' => 'travel',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        // 1. General list
        $response = $this->getJson('/api/v1/authors');
        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data', 'pagination']);

        $this->assertCount(2, $response->json('data'));

        // 2. Search query by name
        $searchRes = $this->getJson('/api/v1/authors?q=Sarah');
        $searchRes->assertStatus(200);
        $this->assertCount(1, $searchRes->json('data'));
        $this->assertSame('Sarah Designer', $searchRes->json('data.0.name'));

        // 3. Filter by specialty
        $specialtyRes = $this->getJson('/api/v1/authors?specialty=Backend');
        $specialtyRes->assertStatus(200);
        $this->assertCount(1, $specialtyRes->json('data'));
        $this->assertSame('John Developer', $specialtyRes->json('data.0.name'));

        // 4. Sort by articles count
        $sortRes = $this->getJson('/api/v1/authors?sort=articles_count');
        $sortRes->assertStatus(200);
        $this->assertSame('John Developer', $sortRes->json('data.0.name'));

        // 5. Paginated list
        $paginatedRes = $this->getJson('/api/v1/authors?per_page=1&page=1');
        $paginatedRes->assertStatus(200)
            ->assertJsonPath('pagination.per_page', 1)
            ->assertJsonPath('pagination.total', 2);
    }

    public function test_authors_featured_and_top_endpoints(): void
    {
        $topAuthor = User::factory()->create([
            'name' => 'Top Star',
            'username' => 'topstar',
            'role' => 'author',
            'is_active' => true,
        ]);

        $fan = User::factory()->create();
        $fan->follows()->attach($topAuthor->id);

        Article::factory()->count(5)->create([
            'user_id' => $topAuthor->id,
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $featuredRes = $this->getJson('/api/v1/authors/featured?limit=1');
        $featuredRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data']);

        $this->assertCount(1, $featuredRes->json('data'));
        $this->assertSame('Top Star', $featuredRes->json('data.0.name'));

        // Alias /authors/top
        $topRes = $this->getJson('/api/v1/authors/top?limit=1');
        $topRes->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertSame('Top Star', $topRes->json('data.0.name'));
    }

    public function test_author_show_by_username_and_id_with_rich_stats(): void
    {
        $author = User::factory()->create([
            'name' => 'Alex Writer',
            'username' => 'alexwriter',
            'role' => 'author',
            'specialty' => 'Cloud Computing',
            'tagline' => 'Writing about clouds',
            'bio' => 'Senior cloud architect.',
            'social' => ['twitter' => '@alexwriter'],
            'is_active' => true,
        ]);

        $reader = User::factory()->create();
        $reader->follows()->attach($author->id);

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Serverless Architecture in AWS',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
            'views' => 150,
            'likes' => 25,
        ]);

        // By username
        $resByUsername = $this->getJson('/api/v1/authors/alexwriter');
        $resByUsername->assertStatus(200)
            ->assertJson([
                'success' => true,
                'author' => [
                    'username' => 'alexwriter',
                    'name' => 'Alex Writer',
                    'specialty' => 'Cloud Computing',
                    'followers_count' => 1,
                    'total_views' => 150,
                    'total_likes' => 25,
                ],
            ])
            ->assertJsonStructure(['author' => ['topics', 'social', 'following_count']]);

        // By ID
        $resById = $this->getJson("/api/v1/authors/{$author->id}");
        $resById->assertStatus(200)
            ->assertJsonPath('author.id', $author->id);

        // 404 for non-existent author
        $notFound = $this->getJson('/api/v1/authors/non-existent-user-xyz');
        $notFound->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_author_dedicated_articles_endpoint(): void
    {
        $author = User::factory()->create(['role' => 'author', 'username' => 'codewriter']);

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Laravel Masterclass',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
            'views' => 500,
            'likes' => 100,
        ]);

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'European Backpacking Trip',
            'category_slug' => 'travel',
            'status' => Article::STATUS_PUBLISHED,
            'views' => 200,
            'likes' => 30,
        ]);

        // 1. All articles for author
        $res = $this->getJson("/api/v1/authors/{$author->username}/articles");
        $res->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['author', 'data', 'pagination']);
        $this->assertCount(2, $res->json('data'));

        // 2. Filter by category
        $catRes = $this->getJson("/api/v1/authors/{$author->username}/articles?category=technology");
        $catRes->assertStatus(200);
        $this->assertCount(1, $catRes->json('data'));
        $this->assertSame('Laravel Masterclass', $catRes->json('data.0.title'));

        // 3. Search within articles
        $searchRes = $this->getJson("/api/v1/authors/{$author->username}/articles?q=Backpacking");
        $searchRes->assertStatus(200);
        $this->assertCount(1, $searchRes->json('data'));
        $this->assertSame('European Backpacking Trip', $searchRes->json('data.0.title'));

        // 4. Sort by popular views
        $sortRes = $this->getJson("/api/v1/authors/{$author->username}/articles?sort=popular");
        $sortRes->assertStatus(200);
        $this->assertSame('Laravel Masterclass', $sortRes->json('data.0.title'));
    }

    public function test_author_followers_and_following_endpoints(): void
    {
        $author = User::factory()->create(['role' => 'author', 'username' => 'guru']);
        $follower = User::factory()->create(['name' => 'Dev Fan', 'username' => 'devfan']);
        $otherAuthor = User::factory()->create(['role' => 'author', 'name' => 'Mentor', 'username' => 'mentor']);

        // Follower follows author
        $follower->follows()->attach($author->id);

        // Author follows otherAuthor
        $author->follows()->attach($otherAuthor->id);

        // Check followers list
        $followersRes = $this->getJson("/api/v1/authors/{$author->username}/followers");
        $followersRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['author', 'data', 'pagination']);
        $this->assertCount(1, $followersRes->json('data'));
        $this->assertSame('Dev Fan', $followersRes->json('data.0.name'));

        // Check following list
        $followingRes = $this->getJson("/api/v1/authors/{$author->username}/following");
        $followingRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['author', 'data', 'pagination']);
        $this->assertCount(1, $followingRes->json('data'));
        $this->assertSame('Mentor', $followingRes->json('data.0.name'));
    }

    public function test_author_follow_unfollow_and_status_actions(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        // Check status (initially false)
        $statusRes = $this->getJson("/api/v1/authors/{$author->id}/follow-status");
        $statusRes->assertStatus(200)
            ->assertJson(['success' => true, 'is_following' => false]);

        // Explicit follow
        $followRes = $this->postJson("/api/v1/authors/{$author->id}/follow");
        $followRes->assertStatus(200)
            ->assertJson(['success' => true, 'is_following' => true, 'followers_count' => 1]);

        // Follow status now true
        $statusRes2 = $this->getJson("/api/v1/authors/{$author->id}/follow-status");
        $statusRes2->assertStatus(200)
            ->assertJson(['success' => true, 'is_following' => true]);

        // Explicit unfollow
        $unfollowRes = $this->postJson("/api/v1/authors/{$author->id}/unfollow");
        $unfollowRes->assertStatus(200)
            ->assertJson(['success' => true, 'is_following' => false, 'followers_count' => 0]);

        // Self-follow returns 400
        $selfFollowRes = $this->actingAs($author)->postJson("/api/v1/authors/{$author->id}/follow");
        $selfFollowRes->assertStatus(400)
            ->assertJson(['success' => false, 'message' => 'You cannot follow yourself.']);
    }

    public function test_author_dashboard_analytics(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $reader = User::factory()->create(['role' => 'reader']);

        // Reader attempting to view dashboard gets 403
        Sanctum::actingAs($reader);
        $deniedRes = $this->getJson('/api/v1/author/dashboard');
        $deniedRes->assertStatus(403)
            ->assertJson(['success' => false]);

        // Author views dashboard
        Sanctum::actingAs($author);

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Article One',
            'status' => Article::STATUS_PUBLISHED,
            'views' => 100,
            'likes' => 10,
        ]);

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Article Two',
            'status' => Article::STATUS_DRAFT,
            'views' => 0,
        ]);

        $dashRes = $this->getJson('/api/v1/author/dashboard');
        $dashRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'stats' => [
                        'total_articles',
                        'published_articles',
                        'draft_articles',
                        'total_views',
                        'total_likes',
                        'total_comments',
                        'followers_count',
                        'following_count',
                    ],
                    'top_articles',
                    'recent_articles',
                ],
            ]);

        $this->assertSame(2, $dashRes->json('data.stats.total_articles'));
        $this->assertSame(1, $dashRes->json('data.stats.published_articles'));
        $this->assertSame(1, $dashRes->json('data.stats.draft_articles'));
        $this->assertSame(100, $dashRes->json('data.stats.total_views'));
    }

    public function test_author_comment_moderation_flow(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $commenter = User::factory()->create();
        $stranger = User::factory()->create(['role' => 'author']);

        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => Article::STATUS_PUBLISHED,
            'comment_count' => 1,
        ]);

        $comment = Comment::create([
            'user_id' => $commenter->id,
            'article_id' => $article->id,
            'content' => 'Needs revision on section 2.',
            'status' => 'pending',
        ]);

        // 1. Author lists comments on their articles
        Sanctum::actingAs($author);
        $listRes = $this->getJson('/api/v1/author/comments?status=pending');
        $listRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data', 'pagination']);
        $this->assertCount(1, $listRes->json('data'));

        // 2. Stranger tries to approve author's comment -> 403 Forbidden
        Sanctum::actingAs($stranger);
        $failApprove = $this->postJson("/api/v1/author/comments/{$comment->id}/approve");
        $failApprove->assertStatus(403);

        // 3. Author approves comment
        Sanctum::actingAs($author);
        $approveRes = $this->postJson("/api/v1/author/comments/{$comment->id}/approve");
        $approveRes->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'status' => 'approved']);

        // 4. Author deletes comment
        $deleteRes = $this->deleteJson("/api/v1/author/comments/{$comment->id}");
        $deleteRes->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
