<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorArticleRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Technology topics',
            'icon' => 'laptop',
        ]);
    }

    /**
     * Rule 1: Author create: status can only be "draft" or "pending". Default is "draft".
     * If "published" is sent, return 422.
     * If "pending" is sent, use submitForReview().
     */
    public function test_author_cannot_create_article_with_published_status(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Direct Publish Attempt',
            'content' => 'Content that should not be published directly.',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseMissing('articles', ['title' => 'Direct Publish Attempt']);
    }

    public function test_author_create_defaults_to_draft(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Default Draft Article',
            'content' => 'Article created without specifying status.',
            'category_slug' => 'technology',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', Article::STATUS_DRAFT);

        $this->assertDatabaseHas('articles', [
            'title' => 'Default Draft Article',
            'status' => Article::STATUS_DRAFT,
            'published_at' => null,
            'submitted_at' => null,
        ]);
    }

    public function test_author_create_with_pending_status_calls_submit_for_review(): void
    {
        $author = User::factory()->create(['role' => 'author', 'name' => 'Sara Author']);
        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Pending Review Article',
            'content' => 'Article submitted directly for review on create.',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PENDING,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', Article::STATUS_PENDING);

        $article = Article::where('title', 'Pending Review Article')->first();
        $this->assertNotNull($article);
        $this->assertEquals(Article::STATUS_PENDING, $article->status);
        $this->assertNotNull($article->submitted_at);
        $this->assertNull($article->published_at);

        // Activity log check
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Submitted',
            'module' => 'Article',
            'subject_id' => $article->id,
        ]);
    }

    /**
     * Rule 2: Update: allowed only if the article belongs to the user AND its status is draft or rejected.
     * If status is pending or published, return 403 with message "This article cannot be edited right now."
     */
    public function test_author_cannot_update_pending_article(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'In Review Article',
            'slug' => 'in-review-article',
            'excerpt' => 'Excerpt',
            'content' => 'Original Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Tampered Title',
            'content' => 'Tampered Content',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This article cannot be edited right now.',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'In Review Article',
            'status' => Article::STATUS_PENDING,
        ]);
    }

    public function test_author_cannot_update_published_article(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Live Published Article',
            'slug' => 'live-published-article',
            'excerpt' => 'Excerpt',
            'content' => 'Original Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Hacked Published Title',
            'content' => 'Hacked Content',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This article cannot be edited right now.',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Live Published Article',
            'status' => Article::STATUS_PUBLISHED,
        ]);
    }

    public function test_author_cannot_update_article_owned_by_another_user(): void
    {
        $owner = User::factory()->create(['role' => 'author']);
        $otherAuthor = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($otherAuthor);

        $article = Article::create([
            'user_id' => $owner->id,
            'title' => 'Owner Article',
            'slug' => 'owner-article',
            'excerpt' => 'Excerpt',
            'content' => 'Original Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_DRAFT,
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Unauthorized Title',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized to edit this article.',
            ]);
    }

    public function test_author_can_update_draft_article(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Original Draft',
            'slug' => 'original-draft',
            'excerpt' => 'Excerpt',
            'content' => 'Draft Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_DRAFT,
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Updated Draft Title',
            'content' => 'Updated draft content with more details.',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.title', 'Updated Draft Title');

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Draft Title',
            'status' => Article::STATUS_DRAFT,
        ]);
    }

    /**
     * Rule 3: When a rejected article is edited and saved, its status becomes draft
     * (keep review_note visible until it is resubmitted).
     */
    public function test_editing_rejected_article_transitions_to_draft_and_preserves_review_note(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Rejected Article',
            'slug' => 'rejected-article',
            'excerpt' => 'Excerpt',
            'content' => 'Needs revision',
            'category_slug' => 'technology',
            'status' => Article::STATUS_REJECTED,
            'review_note' => 'Please expand on section 2 and cite references.',
            'reviewed_at' => now(),
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Rejected Article Fixed',
            'content' => 'Now with expanded section 2 and references.',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $refreshed = $article->fresh();
        // Status must become draft
        $this->assertEquals(Article::STATUS_DRAFT, $refreshed->status);
        // review_note must be preserved
        $this->assertEquals('Please expand on section 2 and cite references.', $refreshed->review_note);
    }

    /**
     * Rule 4: New endpoint POST /api/v1/articles/{id}/submit (auth:sanctum):
     * owner only; allowed only from draft or rejected, otherwise 422;
     * calls submitForReview(); clears review_note; notifies admin / ActivityLog.
     */
    public function test_submit_endpoint_owner_only_and_validates_status(): void
    {
        $author = User::factory()->create(['role' => 'author', 'name' => 'John Author']);
        $stranger = User::factory()->create(['role' => 'author']);

        $draftArticle = Article::create([
            'user_id' => $author->id,
            'title' => 'Article To Submit',
            'slug' => 'article-to-submit',
            'excerpt' => 'Excerpt',
            'content' => 'Content ready for review',
            'category_slug' => 'technology',
            'status' => Article::STATUS_DRAFT,
        ]);

        // 1. Stranger cannot submit
        Sanctum::actingAs($stranger);
        $resStranger = $this->postJson("/api/v1/articles/{$draftArticle->id}/submit");
        $resStranger->assertStatus(403)
            ->assertJson(['success' => false, 'message' => 'Unauthorized to submit this article.']);

        // 2. Owner can submit draft
        Sanctum::actingAs($author);
        $resOwner = $this->postJson("/api/v1/articles/{$draftArticle->id}/submit");
        $resOwner->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Article submitted for review successfully.',
            ])
            ->assertJsonPath('data.status', Article::STATUS_PENDING);

        $draftArticle->refresh();
        $this->assertEquals(Article::STATUS_PENDING, $draftArticle->status);
        $this->assertNotNull($draftArticle->submitted_at);

        // 3. Submitting an already pending article returns 422
        $resPending = $this->postJson("/api/v1/articles/{$draftArticle->id}/submit");
        $resPending->assertStatus(422)
            ->assertJson(['success' => false]);

        // 4. Submitting a published article returns 422
        $publishedArticle = Article::create([
            'user_id' => $author->id,
            'title' => 'Already Published',
            'slug' => 'already-published',
            'excerpt' => 'Excerpt',
            'content' => 'Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        $resPub = $this->postJson("/api/v1/articles/{$publishedArticle->id}/submit");
        $resPub->assertStatus(422);

        // 5. Submitting a rejected article clears review_note and sets pending
        $rejectedArticle = Article::create([
            'user_id' => $author->id,
            'title' => 'Rejected To Resubmit',
            'slug' => 'rejected-to-resubmit',
            'excerpt' => 'Excerpt',
            'content' => 'Revised content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_REJECTED,
            'review_note' => 'Fix typos before resubmitting.',
        ]);

        $resResubmit = $this->postJson("/api/v1/articles/{$rejectedArticle->id}/submit");
        $resResubmit->assertStatus(200)
            ->assertJson(['success' => true]);

        $rejectedArticle->refresh();
        $this->assertEquals(Article::STATUS_PENDING, $rejectedArticle->status);
        $this->assertNull($rejectedArticle->review_note); // review_note is cleared
        $this->assertNotNull($rejectedArticle->submitted_at);

        // ActivityLog assertion
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Submitted',
            'module' => 'Article',
            'subject_id' => $rejectedArticle->id,
        ]);
    }

    /**
     * Rule 5: Admin role keeps its current behavior (can publish directly, can edit any article).
     */
    public function test_admin_can_create_article_directly_as_published(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Admin Direct Publish',
            'content' => 'Published instantly by administrator.',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', Article::STATUS_PUBLISHED);

        $this->assertDatabaseHas('articles', [
            'title' => 'Admin Direct Publish',
            'status' => Article::STATUS_PUBLISHED,
        ]);
    }

    public function test_admin_can_update_published_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($admin);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Live Post To Edit By Admin',
            'slug' => 'live-post-to-edit-by-admin',
            'excerpt' => 'Excerpt',
            'content' => 'Original Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Admin Edited Live Post',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.title', 'Admin Edited Live Post');

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Admin Edited Live Post',
            'status' => Article::STATUS_PUBLISHED,
        ]);
    }

    /**
     * Rule 6: Multipart update:
     * Make sure POST /api/v1/articles/{id} with _method=PUT (and POST /api/v1/articles/{id}/update)
     * works with the "image" file upload.
     */
    public function test_multipart_update_with_method_put_and_image_file(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Draft For Image Upload',
            'slug' => 'draft-for-image-upload',
            'excerpt' => 'Excerpt',
            'content' => 'Content before image',
            'category_slug' => 'technology',
            'status' => Article::STATUS_DRAFT,
        ]);

        $fakeImage = UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg');

        // Call POST /api/v1/articles/{id} with _method=PUT in multipart form-data
        $response = $this->post("/api/v1/articles/{$article->id}", [
            '_method' => 'PUT',
            'title' => 'Draft With Uploaded Image',
            'image' => $fakeImage,
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $article->refresh();
        $this->assertEquals('Draft With Uploaded Image', $article->title);
        $this->assertNotNull($article->image);
        $this->assertStringStartsWith('/storage/articles/', $article->image);

        // Verify stored in disk
        $relativePath = str_replace('/storage/', '', $article->image);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_multipart_update_with_post_update_route(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Draft For POST Route Upload',
            'slug' => 'draft-for-post-route-upload',
            'excerpt' => 'Excerpt',
            'content' => 'Content before upload',
            'category_slug' => 'technology',
            'status' => Article::STATUS_DRAFT,
        ]);

        $fakeImage = UploadedFile::fake()->create('banner.png', 100, 'image/png');

        // Call POST /api/v1/articles/{id}/update directly in multipart form-data
        $response = $this->post("/api/v1/articles/{$article->id}/update", [
            'title' => 'Draft Updated Via POST Route',
            'image' => $fakeImage,
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $article->refresh();
        $this->assertEquals('Draft Updated Via POST Route', $article->title);
        $this->assertNotNull($article->image);
        $this->assertStringStartsWith('/storage/articles/', $article->image);

        $relativePath = str_replace('/storage/', '', $article->image);
        Storage::disk('public')->assertExists($relativePath);
    }

    /**
     * Test 1: Author updates own DRAFT article with status = "published" -> must NOT become published (returns 422).
     */
    public function test_author_updating_own_draft_article_with_published_status_returns_422(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Draft To Publish Attempt',
            'slug' => 'draft-to-publish-attempt',
            'excerpt' => 'Excerpt',
            'content' => 'Original Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_DRAFT,
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Draft To Publish Attempt Updated',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $article->refresh();
        $this->assertEquals(Article::STATUS_DRAFT, $article->status);
        $this->assertNull($article->published_at);
    }

    /**
     * Test 2: Author updates own DRAFT article with status = "pending" -> goes through submitForReview().
     * Review fields (submitted_at) and ActivityLog are recorded.
     */
    public function test_author_updating_own_draft_article_with_pending_status_goes_through_submit_for_review(): void
    {
        $author = User::factory()->create(['role' => 'author', 'name' => 'Alice Author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Draft Ready For Review',
            'slug' => 'draft-ready-for-review',
            'excerpt' => 'Excerpt',
            'content' => 'Original Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_DRAFT,
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Draft Ready For Review (Polished)',
            'status' => Article::STATUS_PENDING,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', Article::STATUS_PENDING);

        $article->refresh();
        $this->assertEquals(Article::STATUS_PENDING, $article->status);
        $this->assertEquals('Draft Ready For Review (Polished)', $article->title);
        $this->assertNotNull($article->submitted_at);
        $this->assertNull($article->published_at);

        // Verify ActivityLog was created
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Submitted',
            'module' => 'Article',
            'subject_id' => $article->id,
        ]);
    }

    /**
     * Test 3: Author updates own REJECTED article with status = "published" -> must not become published (returns 422).
     */
    public function test_author_updating_own_rejected_article_with_published_status_returns_422(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $article = Article::create([
            'user_id' => $author->id,
            'title' => 'Rejected Article Attempt Publish',
            'slug' => 'rejected-article-attempt-publish',
            'excerpt' => 'Excerpt',
            'content' => 'Original Content',
            'category_slug' => 'technology',
            'status' => Article::STATUS_REJECTED,
            'review_note' => 'Admin said needs more work.',
        ]);

        $response = $this->putJson("/api/v1/articles/{$article->id}", [
            'title' => 'Attempting Direct Publish on Rejected',
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $article->refresh();
        $this->assertEquals(Article::STATUS_REJECTED, $article->status);
        $this->assertNull($article->published_at);
    }

    /**
     * Sanitization Test 1:
     * Check that normal content is not damaged: p, h2, strong, em, a, ul, ol, li, blockquote, img stay.
     * Check that <script>, onclick attributes, and javascript: links are removed.
     */
    public function test_html_sanitizer_preserves_allowed_tags_and_removes_malicious_vectors(): void
    {
        $dirtyHtml = '<h2>Clean Heading</h2>'
            . '<p>Paragraph with <strong>bold</strong> and <em>emphasis</em>.</p>'
            . '<ul><li>Bullet 1</li><li>Bullet 2</li></ul>'
            . '<ol><li>Step 1</li><li>Step 2</li></ol>'
            . '<blockquote>Notable quote from author</blockquote>'
            . '<a href="https://example.com" onclick="stealCookies()">Legit Link</a>'
            . '<a href="javascript:alert(\'pwned\')">Malicious Link</a>'
            . '<img src="https://example.com/photo.jpg" alt="Photo" width="400" height="300" loading="lazy" onerror="exploit()">'
            . '<script>alert("XSS Attack");</script>'
            . '<style>body { display:none; }</style>';

        $cleanHtml = HtmlSanitizer::sanitize($dirtyHtml);

        // Allowed tags MUST remain
        $this->assertStringContainsString('<h2>Clean Heading</h2>', $cleanHtml);
        $this->assertStringContainsString('<p>Paragraph with <strong>bold</strong> and <em>emphasis</em>.</p>', $cleanHtml);
        $this->assertStringContainsString('<ul><li>Bullet 1</li><li>Bullet 2</li></ul>', $cleanHtml);
        $this->assertStringContainsString('<ol><li>Step 1</li><li>Step 2</li></ol>', $cleanHtml);
        $this->assertStringContainsString('<blockquote>Notable quote from author</blockquote>', $cleanHtml);
        $this->assertStringContainsString('<a href="https://example.com"', $cleanHtml);
        $this->assertStringContainsString('<img src="https://example.com/photo.jpg"', $cleanHtml);
        $this->assertStringContainsString('alt="Photo"', $cleanHtml);

        // Malicious vectors MUST be removed
        $this->assertStringNotContainsString('<script', $cleanHtml);
        $this->assertStringNotContainsString('XSS Attack', $cleanHtml);
        $this->assertStringNotContainsString('<style', $cleanHtml);
        $this->assertStringNotContainsString('onclick', $cleanHtml);
        $this->assertStringNotContainsString('stealCookies', $cleanHtml);
        $this->assertStringNotContainsString('javascript:', $cleanHtml);
        $this->assertStringNotContainsString('pwned', $cleanHtml);
        $this->assertStringNotContainsString('onerror', $cleanHtml);
        $this->assertStringNotContainsString('exploit', $cleanHtml);
    }

    /**
     * Sanitization Test 2:
     * API store and update endpoints apply HtmlSanitizer to content and excerpt.
     */
    public function test_api_store_and_update_sanitizes_content_and_excerpt(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        Sanctum::actingAs($author);

        $dirtyContent = '<h2>API Heading</h2><p>Safe content with <strong>bold</strong>.</p>'
            . '<script>evilApiCode();</script>'
            . '<a href="https://valid.com" onclick="badApiClick()">Link</a>'
            . '<img src="https://valid.com/img.png" onerror="badApiImg()">';

        $dirtyExcerpt = 'Excerpt with <script>hackExcerpt();</script><strong>bold</strong>';

        // 1. Create via API
        $createRes = $this->postJson('/api/v1/articles', [
            'title' => 'API Sanitized Article',
            'excerpt' => $dirtyExcerpt,
            'content' => $dirtyContent,
            'category_slug' => 'technology',
            'status' => 'draft',
        ]);

        $createRes->assertStatus(201);
        $articleId = $createRes->json('data.id');

        $article = Article::find($articleId);
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertStringNotContainsString('evilApiCode', $article->content);
        $this->assertStringNotContainsString('onclick', $article->content);
        $this->assertStringNotContainsString('onerror', $article->content);
        $this->assertStringContainsString('<h2>API Heading</h2>', $article->content);
        $this->assertStringContainsString('<strong>bold</strong>', $article->content);

        $this->assertStringNotContainsString('<script', $article->excerpt);
        $this->assertStringNotContainsString('hackExcerpt', $article->excerpt);
        $this->assertStringContainsString('<strong>bold</strong>', $article->excerpt);

        // 2. Update via API
        $dirtyUpdateContent = '<p>Updated content with <em>italics</em></p><script>hackUpdate();</script>';
        $dirtyUpdateExcerpt = 'Updated excerpt <a href="javascript:badUrl()">link</a>';

        $updateRes = $this->putJson("/api/v1/articles/{$articleId}", [
            'content' => $dirtyUpdateContent,
            'excerpt' => $dirtyUpdateExcerpt,
        ]);

        $updateRes->assertStatus(200);

        $article->refresh();
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertStringNotContainsString('hackUpdate', $article->content);
        $this->assertStringContainsString('<em>italics</em>', $article->content);

        $this->assertStringNotContainsString('javascript:', $article->excerpt);
        $this->assertStringNotContainsString('badUrl', $article->excerpt);
    }

    /**
     * Sanitization Test 3:
     * Website DashboardController store (create and edit) applies HtmlSanitizer to content and excerpt.
     */
    public function test_website_dashboard_store_and_update_sanitizes_content_and_excerpt(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $this->actingAs($author);

        $dirtyContent = '<h2>Web Heading</h2><p>Safe content with <blockquote>quote</blockquote></p>'
            . '<script>evilWebCode();</script>'
            . '<a href="https://webvalid.com" onclick="badWebClick()">Link</a>';
        $dirtyExcerpt = 'Web excerpt with <script>hackWebExcerpt();</script> text';

        // 1. Create on website via POST /dashboard/posts
        $response = $this->post('/dashboard/posts', [
            'title' => 'Web Sanitized Article',
            'slug' => 'web-sanitized-article',
            'excerpt' => $dirtyExcerpt,
            'content' => $dirtyContent,
            'category_slug' => 'technology',
            'status' => 'draft',
        ]);

        $response->assertRedirect('/dashboard/posts');

        $article = Article::where('slug', 'web-sanitized-article')->first();
        $this->assertNotNull($article);
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertStringNotContainsString('evilWebCode', $article->content);
        $this->assertStringNotContainsString('onclick', $article->content);
        $this->assertStringContainsString('<h2>Web Heading</h2>', $article->content);
        $this->assertStringContainsString('<blockquote>quote</blockquote>', $article->content);

        $this->assertStringNotContainsString('<script', $article->excerpt);
        $this->assertStringNotContainsString('hackWebExcerpt', $article->excerpt);

        // 2. Edit on website via PUT /dashboard/posts/{id}
        $dirtyEditContent = '<p>Edited web content <script>hackEditWeb();</script></p>';
        $dirtyEditExcerpt = 'Edited web excerpt <a href="javascript:exploit()">test</a>';

        $editResponse = $this->put("/dashboard/posts/{$article->id}", [
            'title' => 'Web Sanitized Article Edited',
            'slug' => 'web-sanitized-article-edited',
            'excerpt' => $dirtyEditExcerpt,
            'content' => $dirtyEditContent,
            'category_slug' => 'technology',
            'status' => 'draft',
        ]);

        $editResponse->assertRedirect('/dashboard/posts');

        $article->refresh();
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertStringNotContainsString('hackEditWeb', $article->content);
        $this->assertStringContainsString('<p>Edited web content </p>', $article->content);

        $this->assertStringNotContainsString('javascript:', $article->excerpt);
        $this->assertStringNotContainsString('exploit', $article->excerpt);
    }
}
