<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a paginated listing of articles with advanced filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['user:id,name,username,avatar,role'])
            ->published()
            ->withCount('comments');

        // Search in title, excerpt, content
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category filter (slug or id)
        if ($request->filled('category')) {
            $query->where('category_slug', $request->input('category'));
        }

        // Tag filter
        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->whereJsonContains('tags', $tag);
        }

        // Author filter
        if ($request->filled('author_id')) {
            $query->where('user_id', $request->input('author_id'));
        } elseif ($request->filled('author')) {
            $username = $request->input('author');
            $query->whereHas('user', function ($q) use ($username) {
                $q->where('username', $username);
            });
        }

        // Featured only
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest('published_at'),
            'popular' => $query->orderByDesc('views'),
            'likes' => $query->orderByDesc('likes'),
            'comments' => $query->orderByDesc('comments_count'),
            default => $query->latest('published_at'),
        };

        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);
        $articles = $query->paginate($perPage);

        // Enhance with auth user interaction flags if logged in
        $userId = Auth::guard('sanctum')->id();
        if ($userId) {
            $user = Auth::guard('sanctum')->user();
            $likedArticleIds = $user->articleLikes()->pluck('article_id')->toArray();
            $bookmarkedArticleIds = $user->bookmarks()->pluck('article_id')->toArray();

            $articles->getCollection()->transform(function ($article) use ($likedArticleIds, $bookmarkedArticleIds) {
                $article->is_liked = in_array($article->id, $likedArticleIds, true);
                $article->is_bookmarked = in_array($article->id, $bookmarkedArticleIds, true);
                return $article;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $articles->items(),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'has_more' => $articles->hasMorePages(),
            ],
        ]);
    }

    /**
     * Get live feed of latest articles after a specific timestamp or ID.
     */
    public function feed(Request $request): JsonResponse
    {
        $after = $request->input('after'); // ISO timestamp or cursor

        $query = Article::with(['user:id,name,username,avatar'])
            ->published()
            ->latest('published_at');

        if ($after) {
            $query->where('published_at', '>', $after);
        }

        $articles = $query->limit(10)->get();

        return response()->json([
            'success' => true,
            'count' => $articles->count(),
            'data' => $articles,
        ]);
    }

    /**
     * Get featured and trending articles for hero/carousel sections.
     */
    public function highlights(): JsonResponse
    {
        $featured = Article::with(['user:id,name,username,avatar'])
            ->published()
            ->where('is_featured', true)
            ->latest('published_at')
            ->limit(5)
            ->get();

        $trending = Article::with(['user:id,name,username,avatar'])
            ->published()
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'featured' => $featured,
                'trending' => $trending,
            ],
        ]);
    }

    /**
     * Show single article detail by slug or ID with relations & related posts.
     */
    public function show(string $identifier): JsonResponse
    {
        $article = Article::with([
            'user:id,name,username,avatar,role,bio,specialty,tagline',
            'comments' => function ($q) {
                $q->whereNull('parent_id')
                    ->where('status', 'approved')
                    ->with(['user:id,name,username,avatar', 'replies.user:id,name,username,avatar'])
                    ->latest();
            },
        ])
            ->where(function ($q) use ($identifier) {
                if (is_numeric($identifier)) {
                    $q->where('id', $identifier);
                } else {
                    $q->where('slug', $identifier);
                }
            })
            ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        // Increment view count safely
        $article->increment('views');

        // Check user interaction state
        $user = Auth::guard('sanctum')->user();
        $isLiked = false;
        $isBookmarked = false;
        $isFollowingAuthor = false;

        if ($user) {
            $isLiked = $user->articleLikes()->where('article_id', $article->id)->exists();
            $isBookmarked = $user->bookmarks()->where('article_id', $article->id)->exists();
            $isFollowingAuthor = $user->follows()->where('author_id', $article->user_id)->exists();
        }

        // Related articles in same category
        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category_slug', $article->category_slug)
            ->with(['user:id,name,username,avatar'])
            ->latest('published_at')
            ->limit(4)
            ->get();

        $data = $article->toArray();
        $data['comment_count'] = $article->comments()->where('status', 'approved')->count();
        $data['comments_count'] = $data['comment_count'];
        $data['is_liked'] = $isLiked;
        $data['is_bookmarked'] = $isBookmarked;
        $data['is_following_author'] = $isFollowingAuthor;
        $data['related_articles'] = $related;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Create a new article.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_slug' => 'required|string|max:100',
            'tags' => 'nullable',
            'image' => 'nullable|image|max:5120', // max 5MB
            'image_url' => 'nullable|url',
            'status' => 'nullable|in:draft,pending,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestedStatus = $request->input('status');

        // Rule 1 & 5: Authors can only create as draft or pending (default draft).
        // If author sends published, return 422. Admins can create directly as published.
        if ($user->role !== 'admin') {
            if ($requestedStatus === Article::STATUS_PUBLISHED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => [
                        'status' => ['Authors cannot publish articles directly. Status must be draft or pending.'],
                    ],
                ], 422);
            }

            $initialStatus = Article::STATUS_DRAFT;
        } else {
            $initialStatus = $requestedStatus ?: Article::STATUS_PUBLISHED;
        }

        // Generate slug
        $baseSlug = Str::slug($request->input('title'));
        $slug = $baseSlug;
        $counter = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Handle Image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
            $imagePath = '/storage/' . $imagePath;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        // Parse tags
        $tags = $request->input('tags');
        if (is_string($tags)) {
            $tags = array_map('trim', explode(',', $tags));
        }

        // Sanitize content and excerpt
        $content = HtmlSanitizer::sanitize($request->input('content'));
        $excerpt = $request->filled('excerpt')
            ? HtmlSanitizer::sanitize($request->input('excerpt'))
            : Str::limit(strip_tags($content), 150);

        // Reading time calculation (avg 200 words per minute)
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, ceil($wordCount / 200));

        $article = Article::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'category_slug' => $request->input('category_slug'),
            'tags' => $tags ?: [],
            'image' => $imagePath,
            'reading_time' => $readingTime,
            'status' => $initialStatus,
            'published_at' => ($user->role === 'admin' && $initialStatus === Article::STATUS_PUBLISHED) ? now() : null,
        ]);

        // If author requested pending, submit it for review
        if ($user->role !== 'admin' && $requestedStatus === Article::STATUS_PENDING) {
            $article->submitForReview();
        }

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully.',
            'data' => $article->fresh()->load('user:id,name,username,avatar'),
        ], 201);
    }

    /**
     * Update an existing article.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $user = $request->user();

        // Check ownership
        if ($article->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this article.',
            ], 403);
        }

        // Rule 2 & 5: Authors can only edit draft or rejected articles.
        // If status is pending or published, return 403 with exact message.
        if ($user->role !== 'admin') {
            if ($article->status === Article::STATUS_PENDING || $article->status === Article::STATUS_PUBLISHED) {
                return response()->json([
                    'success' => false,
                    'message' => 'This article cannot be edited right now.',
                ], 403);
            }

            if (!in_array($article->status, [Article::STATUS_DRAFT, Article::STATUS_REJECTED], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This article cannot be edited right now.',
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'sometimes|required|string',
            'category_slug' => 'sometimes|required|string|max:100',
            'tags' => 'nullable',
            'image' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
            'status' => 'nullable|in:draft,pending,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($user->role !== 'admin' && $request->input('status') === Article::STATUS_PUBLISHED) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'status' => ['Authors cannot publish articles directly.'],
                ],
            ], 422);
        }

        $data = $request->only(['title', 'category_slug']);
        if ($request->has('content')) {
            $data['content'] = HtmlSanitizer::sanitize($request->input('content'));
        }
        if ($request->has('excerpt')) {
            $data['excerpt'] = $request->filled('excerpt') ? HtmlSanitizer::sanitize($request->input('excerpt')) : null;
        }
        $shouldSubmitForReview = false;

        // Rule 3: When a rejected article is edited and saved, its status becomes draft
        // (keep review_note visible until it is resubmitted).
        if ($user->role !== 'admin') {
            if ($article->status === Article::STATUS_REJECTED) {
                $data['status'] = Article::STATUS_DRAFT;
            } elseif ($request->input('status') === Article::STATUS_PENDING) {
                // If author submits status = 'pending' on draft, it must go through submitForReview()
                // so review fields (submitted_at) and ActivityLog are never skipped.
                $shouldSubmitForReview = true;
            } elseif ($request->filled('status')) {
                $data['status'] = Article::STATUS_DRAFT;
            }
        } else {
            if ($request->filled('status')) {
                $data['status'] = $request->input('status');
                if ($data['status'] === Article::STATUS_PUBLISHED && !$article->published_at) {
                    $data['published_at'] = now();
                }
            }
        }

        if ($request->filled('title') && $request->input('title') !== $article->title) {
            $baseSlug = Str::slug($request->input('title'));
            $slug = $baseSlug;
            $counter = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
            $data['image'] = '/storage/' . $imagePath;
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        if ($request->has('tags')) {
            $tags = $request->input('tags');
            $data['tags'] = is_string($tags) ? array_map('trim', explode(',', $tags)) : $tags;
        }

        if (isset($data['content'])) {
            $wordCount = str_word_count(strip_tags($data['content']));
            $data['reading_time'] = max(1, ceil($wordCount / 200));
        }

        $article->update($data);

        if ($shouldSubmitForReview) {
            $article->submitForReview();
        }

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully.',
            'data' => $article->fresh()->load('user:id,name,username,avatar'),
        ]);
    }

    /**
     * Submit an article for editorial review.
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $user = $request->user();

        // Owner only check (or admin)
        if ($article->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to submit this article.',
            ], 403);
        }

        // Allowed only from draft or rejected status
        if (!in_array($article->status, [Article::STATUS_DRAFT, Article::STATUS_REJECTED], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Article can only be submitted for review from draft or rejected status.',
            ], 422);
        }

        // Calls submitForReview() which sets status to pending, submitted_at = now(),
        // clears review_note, reviewed_at, reviewed_by, and logs ActivityLog (same as website)
        $article->submitForReview();

        return response()->json([
            'success' => true,
            'message' => 'Article submitted for review successfully.',
            'data' => $article->fresh()->load('user:id,name,username,avatar'),
        ]);
    }

    /**
     * Delete an article.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $user = $request->user();

        if ($article->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this article.',
            ], 403);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully.',
        ]);
    }

    /**
     * Toggle like on an article.
     */
    public function toggleLike(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $user = $request->user();
        $liked = $user->articleLikes()->where('article_id', $id)->exists();

        if ($liked) {
            $user->articleLikes()->detach($id);
            $article->decrement('likes');
            $isLiked = false;
        } else {
            $user->articleLikes()->attach($id);
            $article->increment('likes');
            $isLiked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $isLiked,
            'likes_count' => $article->fresh()->likes,
        ]);
    }

    /**
     * Toggle bookmark on an article.
     */
    public function toggleBookmark(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $user = $request->user();
        $bookmarked = $user->bookmarks()->where('article_id', $id)->exists();

        if ($bookmarked) {
            $user->bookmarks()->detach($id);
            $isBookmarked = false;
        } else {
            $user->bookmarks()->attach($id);
            $isBookmarked = true;
        }

        return response()->json([
            'success' => true,
            'bookmarked' => $isBookmarked,
        ]);
    }
}
