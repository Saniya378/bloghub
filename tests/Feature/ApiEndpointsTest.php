<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Tech news and tutorials',
            'icon' => 'laptop',
        ]);
    }

    public function test_auth_register_and_login_flow(): void
    {
        // 1. Register
        $registerRes = $this->postJson('/api/v1/auth/register', [
            'name' => 'Ali Khan',
            'email' => 'ali@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $registerRes->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Registration successful',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'ali@example.com']);

        // 2. Login
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'email' => 'ali@example.com',
            'password' => 'Password123!',
        ]);

        $loginRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
            ]);

        $token = $loginRes->json('data.token');

        // 3. Me endpoint with token
        $meRes = $this->withToken($token)->getJson('/api/v1/auth/me');
        $meRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => ['email' => 'ali@example.com'],
                ],
            ]);

        // 4. Logout
        $logoutRes = $this->withToken($token)->postJson('/api/v1/auth/logout');
        $logoutRes->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Logged out successfully']);
    }

    public function test_password_reset_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'resetuser@example.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        // 1. Forgot password
        $forgotRes = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'resetuser@example.com',
        ]);
        $forgotRes->assertStatus(200)->assertJson(['success' => true]);
        $otp = $forgotRes->json('otp');
        $this->assertNotNull($otp);

        // 2. Verify OTP
        $verifyRes = $this->postJson('/api/v1/auth/verify-otp', [
            'email' => 'resetuser@example.com',
            'otp' => $otp,
        ]);
        $verifyRes->assertStatus(200)->assertJson(['success' => true]);
        $resetToken = $verifyRes->json('reset_token');
        $this->assertNotNull($resetToken);

        // 3. Reset password
        $resetRes = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'resetuser@example.com',
            'reset_token' => $resetToken,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $resetRes->assertStatus(200)->assertJson(['success' => true]);

        // 4. Test login with new password
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'email' => 'resetuser@example.com',
            'password' => 'NewPassword123!',
        ]);
        $loginRes->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_articles_public_and_search_endpoints(): void
    {
        $author = User::factory()->create(['role' => 'author', 'username' => 'techauthor', 'is_active' => true]);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Mastering Flutter & Laravel',
            'slug' => 'mastering-flutter-and-laravel',
            'excerpt' => 'A guide to fullstack mobile app development',
            'content' => 'Full detailed content on Flutter and Laravel API integration.',
            'category_slug' => 'technology',
            'tags' => ['flutter', 'laravel', 'api'],
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
            'reading_time' => 3,
            'views' => 10,
            'likes' => 5,
        ]);

        // 1. Articles index
        $indexRes = $this->getJson('/api/v1/articles');
        $indexRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data', 'pagination']);
        $this->assertCount(1, $indexRes->json('data'));

        // 2. Feed endpoint
        $feedRes = $this->getJson('/api/v1/articles/feed');
        $feedRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // 3. Highlights
        $highlightsRes = $this->getJson('/api/v1/articles/highlights');
        $highlightsRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['featured', 'trending']]);

        // 4. Show single article
        $showRes = $this->getJson('/api/v1/articles/mastering-flutter-and-laravel');
        $showRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'slug' => 'mastering-flutter-and-laravel',
                    'title' => 'Mastering Flutter & Laravel',
                ],
            ]);

        // 5. Search endpoint
        $searchRes = $this->getJson('/api/v1/search?q=Flutter');
        $searchRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['articles', 'authors', 'categories']]);

        // 6. Suggestions
        $sugRes = $this->getJson('/api/v1/search/suggestions?q=Flu');
        $sugRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['suggestions' => ['articles', 'authors', 'categories']]);
    }

    public function test_categories_and_authors_endpoints(): void
    {
        $author = User::factory()->create([
            'role' => 'author',
            'username' => 'janeauthor',
            'is_active' => true,
        ]);

        // 1. Categories
        $catRes = $this->getJson('/api/v1/categories');
        $catRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data']);

        $catShowRes = $this->getJson('/api/v1/categories/technology');
        $catShowRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['category', 'articles', 'pagination']);

        // 2. Authors
        $authorsRes = $this->getJson('/api/v1/authors');
        $authorsRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data']);

        $authorShowRes = $this->getJson('/api/v1/authors/janeauthor');
        $authorShowRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['author', 'articles', 'pagination']);
    }

    public function test_general_endpoints(): void
    {
        // Stats
        $statsRes = $this->getJson('/api/v1/stats');
        $statsRes->assertStatus(200)->assertJson(['success' => true]);

        // Contact
        $contactRes = $this->postJson('/api/v1/contact', [
            'name' => 'John Client',
            'email' => 'client@test.com',
            'subject' => 'App inquiry',
            'message' => 'Testing mobile contact API.',
        ]);
        $contactRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('contact_messages', ['email' => 'client@test.com']);

        // Newsletter
        $newsRes = $this->postJson('/api/v1/newsletter/subscribe', [
            'email' => 'newsletter@test.com',
        ]);
        $newsRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'newsletter@test.com']);
    }

    public function test_authenticated_article_crud_and_interactions(): void
    {
        $user = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($user);

        // 1. Create Article
        $createRes = $this->postJson('/api/v1/articles', [
            'title' => 'Flutter Mobile Architecture',
            'content' => 'Detailed architecture for Riverpod, Bloc, and Clean Architecture in Flutter apps.',
            'category_slug' => 'technology',
            'tags' => ['flutter', 'mobile'],
            'status' => 'published',
        ]);

        $createRes->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.title', 'Flutter Mobile Architecture');

        $articleId = $createRes->json('data.id');
        $this->assertNotNull($articleId);

        // 2. Update Article
        $updateRes = $this->putJson("/api/v1/articles/{$articleId}", [
            'title' => 'Flutter Mobile Architecture 2026',
            'content' => 'Updated content for 2026.',
            'category_slug' => 'technology',
        ]);
        $updateRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('articles', ['id' => $articleId, 'title' => 'Flutter Mobile Architecture 2026']);

        // 3. Toggle Like Article
        $likeRes = $this->postJson("/api/v1/articles/{$articleId}/toggle-like");
        $likeRes->assertStatus(200)->assertJson(['success' => true, 'liked' => true]);

        // 4. Toggle Bookmark Article
        $bookmarkRes = $this->postJson("/api/v1/articles/{$articleId}/toggle-bookmark");
        $bookmarkRes->assertStatus(200)->assertJson(['success' => true, 'bookmarked' => true]);

        // 5. Post Comment
        $commentRes = $this->postJson("/api/v1/articles/{$articleId}/comments", [
            'content' => 'Great article on Flutter!',
        ]);
        $commentRes->assertStatus(201)->assertJson(['success' => true]);
        $commentId = $commentRes->json('data.id');

        // 6. Toggle Like Comment
        $commentLikeRes = $this->postJson("/api/v1/comments/{$commentId}/toggle-like");
        $commentLikeRes->assertStatus(200)->assertJson(['success' => true, 'liked' => true]);

        // 7. Get Comments list
        $commentsListRes = $this->getJson("/api/v1/articles/{$articleId}/comments");
        $commentsListRes->assertStatus(200)->assertJson(['success' => true]);

        // 8. Delete Comment
        $deleteCommentRes = $this->deleteJson("/api/v1/comments/{$commentId}");
        $deleteCommentRes->assertStatus(200)->assertJson(['success' => true]);

        // 9. Profile show & stats
        $profileRes = $this->getJson('/api/v1/profile');
        $profileRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['user', 'stats']]);

        // 10. Bookmarks & Favorites & My Articles
        $bookmarksRes = $this->getJson('/api/v1/profile/bookmarks');
        $bookmarksRes->assertStatus(200)->assertJson(['success' => true]);

        $favRes = $this->getJson('/api/v1/profile/favorites');
        $favRes->assertStatus(200)->assertJson(['success' => true]);

        $myArticlesRes = $this->getJson('/api/v1/profile/my-articles');
        $myArticlesRes->assertStatus(200)->assertJson(['success' => true]);

        // 11. Delete Article
        $deleteArticleRes = $this->deleteJson("/api/v1/articles/{$articleId}");
        $deleteArticleRes->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_author_follow_and_notifications(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $reader = User::factory()->create(['role' => 'reader']);

        Sanctum::actingAs($reader);

        // 1. Follow author
        $followRes = $this->postJson("/api/v1/authors/{$author->id}/toggle-follow");
        $followRes->assertStatus(200)->assertJson(['success' => true, 'is_following' => true]);

        // 2. Unfollow author
        $unfollowRes = $this->postJson("/api/v1/authors/{$author->id}/toggle-follow");
        $unfollowRes->assertStatus(200)->assertJson(['success' => true, 'is_following' => false]);

        // 3. Notifications list
        $notifRes = $this->getJson('/api/v1/notifications');
        $notifRes->assertStatus(200)->assertJson(['success' => true]);

        // 4. Mark all read
        $markAllRes = $this->postJson('/api/v1/notifications/read-all');
        $markAllRes->assertStatus(200)->assertJson(['success' => true]);
    }
}
