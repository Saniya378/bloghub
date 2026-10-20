<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    /**
     * List all active authors with article counts, follower stats,
     * optional search (q/search), specialty filter, sorting, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::whereIn('role', ['author', 'admin'])
            ->where('is_active', true)
            ->withCount([
                'articles as published_articles_count' => function ($q) {
                    $q->published();
                },
                'followers',
                'follows as following_count',
            ]);

        // Search by name, username, specialty, tagline, or bio
        if ($request->filled('q') || $request->filled('search')) {
            $search = (string) $request->input('q', $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('specialty', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        // Filter by specialty
        if ($request->filled('specialty')) {
            $specialty = (string) $request->input('specialty');
            $query->where('specialty', 'like', "%{$specialty}%");
        }

        // Sorting
        $sort = $request->input('sort', 'articles_count');
        match ($sort) {
            'popular', 'followers', 'most_followed' => $query->orderByDesc('followers_count')->orderByDesc('published_articles_count'),
            'latest', 'newest' => $query->latest('created_at'),
            'name', 'alphabetical' => $query->orderBy('name', 'asc'),
            default => $query->orderByDesc('published_articles_count')->orderByDesc('followers_count'),
        };

        $currentUser = Auth::guard('sanctum')->user();
        $followedAuthorIds = $currentUser ? $currentUser->follows()->pluck('author_id')->flip()->all() : [];

        // Check if pagination was requested or defaults to full list with pagination metadata
        if ($request->has('page') || $request->has('per_page') || $request->boolean('paginate')) {
            $perPage = min(max((int) $request->input('per_page', 12), 1), 50);
            $paginated = $query->paginate($perPage);

            $authors = collect($paginated->items())->map(function ($author) use ($followedAuthorIds) {
                return $this->formatAuthorSummary($author, isset($followedAuthorIds[$author->id]));
            });

            return response()->json([
                'success' => true,
                'data' => $authors,
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'has_more' => $paginated->hasMorePages(),
                ],
            ]);
        }

        $authors = $query->get()->map(function ($author) use ($followedAuthorIds) {
            return $this->formatAuthorSummary($author, isset($followedAuthorIds[$author->id]));
        });

        return response()->json([
            'success' => true,
            'data' => $authors,
            'pagination' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => $authors->count(),
                'total' => $authors->count(),
                'has_more' => false,
            ],
        ]);
    }

    /**
     * Get top / featured authors by followers and articles count.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->input('limit', 6), 1), 20);

        $currentUser = Auth::guard('sanctum')->user();
        $followedAuthorIds = $currentUser ? $currentUser->follows()->pluck('author_id')->flip()->all() : [];

        $authors = User::whereIn('role', ['author', 'admin'])
            ->where('is_active', true)
            ->withCount([
                'articles as published_articles_count' => function ($q) {
                    $q->published();
                },
                'followers',
                'follows as following_count',
            ])
            ->orderByDesc('followers_count')
            ->orderByDesc('published_articles_count')
            ->take($limit)
            ->get()
            ->map(function ($author) use ($followedAuthorIds) {
                return $this->formatAuthorSummary($author, isset($followedAuthorIds[$author->id]));
            });

        return response()->json([
            'success' => true,
            'data' => $authors,
        ]);
    }

    /**
     * Get author profile details and their published articles.
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        $author = $this->findAuthor($identifier);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $perPage = min(max((int) $request->input('per_page', 10), 1), 30);
        $articles = Article::published()
            ->where('user_id', $author->id)
            ->latest('published_at')
            ->paginate($perPage);

        $isFollowing = false;
        $currentUser = Auth::guard('sanctum')->user();
        if ($currentUser) {
            $isFollowing = $currentUser->follows()->where('author_id', $author->id)->exists();
        }

        $totalViews = Article::published()->where('user_id', $author->id)->sum('views');
        $totalLikes = Article::published()->where('user_id', $author->id)->sum('likes');
        $topics = Article::published()
            ->where('user_id', $author->id)
            ->distinct()
            ->pluck('category_slug')
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'author' => [
                'id' => $author->id,
                'name' => $author->name,
                'username' => $author->username,
                'avatar' => $author->avatar,
                'cover_image' => $author->cover_image,
                'bio' => $author->bio,
                'tagline' => $author->tagline,
                'specialty' => $author->specialty,
                'social' => is_string($author->social) ? json_decode($author->social, true) : ($author->social ?? []),
                'role' => $author->role,
                'articles_count' => (int) $author->published_articles_count,
                'followers_count' => (int) $author->followers_count,
                'following_count' => (int) $author->following_count,
                'total_views' => (int) $totalViews,
                'total_likes' => (int) $totalLikes,
                'topics' => $topics,
                'is_following' => $isFollowing,
            ],
            'articles' => $articles->items(),
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
     * Get published articles by a specific author with filters (category, tag, search, sort).
     */
    public function articles(Request $request, string $identifier): JsonResponse
    {
        $author = $this->findAuthor($identifier);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $query = Article::published()
            ->where('user_id', $author->id)
            ->with(['user:id,name,username,avatar'])
            ->withCount('comments');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_slug', $request->input('category'));
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->input('tag'));
        }

        // Search within author's articles
        if ($request->filled('q') || $request->filled('search')) {
            $search = (string) $request->input('q', $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sort articles
        match ($request->input('sort')) {
            'popular', 'views' => $query->orderByDesc('views'),
            'likes' => $query->orderByDesc('likes'),
            'oldest' => $query->oldest('published_at'),
            default => $query->latest('published_at'),
        };

        $perPage = min(max((int) $request->input('per_page', 10), 1), 30);
        $articles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'author' => [
                'id' => $author->id,
                'name' => $author->name,
                'username' => $author->username,
            ],
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
     * Get paginated list of users following an author.
     */
    public function followers(Request $request, string $identifier): JsonResponse
    {
        $author = $this->findAuthor($identifier);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $followers = $author->followers()
            ->where('is_active', true)
            ->select(['users.id', 'users.name', 'users.username', 'users.avatar', 'users.bio', 'users.tagline'])
            ->latest('follows.created_at')
            ->paginate($perPage);

        $currentUser = Auth::guard('sanctum')->user();
        $followedAuthorIds = $currentUser ? $currentUser->follows()->pluck('author_id')->flip()->all() : [];

        $items = collect($followers->items())->map(function ($follower) use ($followedAuthorIds) {
            return [
                'id' => $follower->id,
                'name' => $follower->name,
                'username' => $follower->username,
                'avatar' => $follower->avatar,
                'bio' => $follower->bio,
                'tagline' => $follower->tagline,
                'is_following' => isset($followedAuthorIds[$follower->id]),
            ];
        });

        return response()->json([
            'success' => true,
            'author' => [
                'id' => $author->id,
                'name' => $author->name,
                'username' => $author->username,
                'followers_count' => (int) $author->followers_count,
            ],
            'data' => $items,
            'pagination' => [
                'current_page' => $followers->currentPage(),
                'last_page' => $followers->lastPage(),
                'per_page' => $followers->perPage(),
                'total' => $followers->total(),
                'has_more' => $followers->hasMorePages(),
            ],
        ]);
    }

    /**
     * Get paginated list of authors followed by this author.
     */
    public function following(Request $request, string $identifier): JsonResponse
    {
        $author = $this->findAuthor($identifier);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $following = $author->follows()
            ->where('is_active', true)
            ->withCount([
                'articles as published_articles_count' => function ($q) {
                    $q->published();
                },
                'followers',
            ])
            ->select(['users.id', 'users.name', 'users.username', 'users.avatar', 'users.bio', 'users.tagline', 'users.specialty'])
            ->latest('follows.created_at')
            ->paginate($perPage);

        $currentUser = Auth::guard('sanctum')->user();
        $followedAuthorIds = $currentUser ? $currentUser->follows()->pluck('author_id')->flip()->all() : [];

        $items = collect($following->items())->map(function ($followed) use ($followedAuthorIds) {
            return [
                'id' => $followed->id,
                'name' => $followed->name,
                'username' => $followed->username,
                'avatar' => $followed->avatar,
                'bio' => $followed->bio,
                'tagline' => $followed->tagline,
                'specialty' => $followed->specialty,
                'articles_count' => (int) $followed->published_articles_count,
                'followers_count' => (int) $followed->followers_count,
                'is_following' => isset($followedAuthorIds[$followed->id]),
            ];
        });

        return response()->json([
            'success' => true,
            'author' => [
                'id' => $author->id,
                'name' => $author->name,
                'username' => $author->username,
                'following_count' => (int) $author->following_count,
            ],
            'data' => $items,
            'pagination' => [
                'current_page' => $following->currentPage(),
                'last_page' => $following->lastPage(),
                'per_page' => $following->perPage(),
                'total' => $following->total(),
                'has_more' => $following->hasMorePages(),
            ],
        ]);
    }

    /**
     * Follow or unfollow an author (toggle).
     */
    public function toggleFollow(Request $request, int $id): JsonResponse
    {
        $author = User::find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $user = $request->user();

        if ($user->id === $author->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself.',
            ], 400);
        }

        $isFollowing = $user->follows()->where('author_id', $id)->exists();

        if ($isFollowing) {
            $user->follows()->detach($id);
            $following = false;
        } else {
            $user->follows()->attach($id);
            $following = true;
        }

        return response()->json([
            'success' => true,
            'is_following' => $following,
            'followers_count' => $author->followers()->count(),
        ]);
    }

    /**
     * Explicitly follow an author (idempotent).
     */
    public function follow(Request $request, int $id): JsonResponse
    {
        $author = User::find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $user = $request->user();

        if ($user->id === $author->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself.',
            ], 400);
        }

        if (!$user->follows()->where('author_id', $id)->exists()) {
            $user->follows()->attach($id);
        }

        return response()->json([
            'success' => true,
            'message' => "You are now following {$author->name}.",
            'is_following' => true,
            'followers_count' => $author->followers()->count(),
        ]);
    }

    /**
     * Explicitly unfollow an author (idempotent).
     */
    public function unfollow(Request $request, int $id): JsonResponse
    {
        $author = User::find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $user = $request->user();
        $user->follows()->detach($id);

        return response()->json([
            'success' => true,
            'message' => "You have unfollowed {$author->name}.",
            'is_following' => false,
            'followers_count' => $author->followers()->count(),
        ]);
    }

    /**
     * Get follow status for an author relative to the authenticated user.
     */
    public function followStatus(Request $request, int $id): JsonResponse
    {
        $author = User::find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Author not found.',
            ], 404);
        }

        $user = $request->user();
        $isFollowing = $user ? $user->follows()->where('author_id', $id)->exists() : false;

        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'followers_count' => $author->followers()->count(),
        ]);
    }

    /**
     * Get author dashboard analytics and performance metrics.
     * Accessible by users with role 'author' or 'admin'.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'author' && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You must be registered as an author to access the author dashboard.',
            ], 403);
        }

        $articleIds = $user->articles()->pluck('id');

        $stats = [
            'total_articles' => $user->articles()->count(),
            'published_articles' => $user->articles()->published()->count(),
            'draft_articles' => $user->articles()->draft()->count(),
            'pending_articles' => $user->articles()->pending()->count(),
            'rejected_articles' => $user->articles()->rejected()->count(),
            'total_views' => (int) $user->articles()->sum('views'),
            'total_likes' => (int) $user->articles()->sum('likes'),
            'total_comments' => Comment::whereIn('article_id', $articleIds)->count(),
            'pending_comments' => Comment::whereIn('article_id', $articleIds)->where('status', 'pending')->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->follows()->count(),
        ];

        $topArticles = $user->articles()
            ->published()
            ->orderByDesc('views')
            ->take(5)
            ->get(['id', 'title', 'slug', 'views', 'likes', 'category_slug', 'published_at']);

        $recentArticles = $user->articles()
            ->latest('updated_at')
            ->take(5)
            ->get(['id', 'title', 'slug', 'status', 'views', 'likes', 'category_slug', 'created_at', 'updated_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'top_articles' => $topArticles,
                'recent_articles' => $recentArticles,
            ],
        ]);
    }

    /**
     * List comments on the author's own articles for moderation.
     */
    public function authorComments(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'author' && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You must be registered as an author to moderate comments.',
            ], 403);
        }

        $articleIds = $user->articles()->pluck('id');

        $query = Comment::with([
                'user:id,name,username,avatar',
                'article:id,title,slug',
            ])
            ->whereIn('article_id', $articleIds);

        if ($request->filled('status')) {
            $status = $request->input('status');
            if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('article_id')) {
            $query->where('article_id', $request->input('article_id'));
        }

        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);
        $comments = $query->latest()->paginate($perPage);

        $formatted = collect($comments->items())->map(function ($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'status' => $comment->status,
                'likes' => (int) $comment->likes,
                'created_at' => $comment->created_at?->toISOString() ?? (string) $comment->created_at,
                'user_id' => $comment->user_id,
                'commenter_name' => $comment->user?->name,
                'commenter_avatar' => $comment->user?->avatar,
                'user' => $comment->user ? [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'username' => $comment->user->username,
                    'avatar' => $comment->user->avatar,
                ] : null,
                'article_id' => $comment->article_id,
                'article_title' => $comment->article?->title,
                'article' => $comment->article ? [
                    'id' => $comment->article->id,
                    'title' => $comment->article->title,
                    'slug' => $comment->article->slug,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted,
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
                'has_more' => $comments->hasMorePages(),
            ],
        ]);
    }

    /**
     * Approve a comment on an author's article.
     */
    public function approveComment(Request $request, int $id): JsonResponse
    {
        $comment = Comment::with('article')->find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        $user = $request->user();

        // Must be author of the article or platform admin
        if (!$comment->article || ($comment->article->user_id !== $user->id && $user->role !== 'admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You can only moderate comments on your own articles.',
            ], 403);
        }

        $wasApproved = $comment->status === 'approved';
        $comment->update(['status' => 'approved']);

        if (!$wasApproved && $comment->article) {
            $comment->article->increment('comment_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment approved successfully.',
            'data' => $comment,
        ]);
    }

    /**
     * Delete a comment on an author's article.
     */
    public function deleteComment(Request $request, int $id): JsonResponse
    {
        $comment = Comment::with('article')->find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        $user = $request->user();

        // Must be author of the article or platform admin
        if (!$comment->article || ($comment->article->user_id !== $user->id && $user->role !== 'admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete comments on your own articles.',
            ], 403);
        }

        $wasApproved = $comment->status === 'approved';
        $replies = count($comment->descendantIds());
        $article = $comment->article;
        $comment->delete();

        if ($article && $wasApproved && $article->comment_count > 0) {
            $article->decrement('comment_count', min($article->comment_count, $replies + 1));
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully.',
        ]);
    }

    /**
     * Helper to find an active author by id or username with metrics.
     */
    protected function findAuthor(string $identifier): ?User
    {
        return User::where('is_active', true)
            ->where(function ($q) use ($identifier) {
                if (is_numeric($identifier)) {
                    $q->where('id', $identifier);
                } else {
                    $q->where('username', $identifier);
                }
            })
            ->withCount([
                'articles as published_articles_count' => function ($q) {
                    $q->published();
                },
                'followers',
                'follows as following_count',
            ])
            ->first();
    }

    /**
     * Helper to format author summary array.
     */
    protected function formatAuthorSummary(User $author, bool $isFollowing = false): array
    {
        return [
            'id' => $author->id,
            'name' => $author->name,
            'username' => $author->username,
            'avatar' => $author->avatar,
            'cover_image' => $author->cover_image,
            'bio' => $author->bio,
            'tagline' => $author->tagline,
            'specialty' => $author->specialty,
            'role' => $author->role,
            'articles_count' => (int) $author->published_articles_count,
            'followers_count' => (int) $author->followers_count,
            'following_count' => (int) ($author->following_count ?? 0),
            'is_following' => $isFollowing,
        ];
    }
}
