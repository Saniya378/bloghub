<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile / Frontend Applications
|--------------------------------------------------------------------------
| All routes are prefixed with /api automatically.
| Versioned under /v1 (and direct fallback routes provided).
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // AUTHENTICATION & PASSWORD RESET (PUBLIC)
    // ==========================================
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // ==========================================
    // ARTICLES / BLOG POSTS (PUBLIC)
    // ==========================================
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/feed', [ArticleController::class, 'feed']);
    Route::get('/articles/highlights', [ArticleController::class, 'highlights']);
    Route::get('/articles/{identifier}', [ArticleController::class, 'show']);
    Route::get('/articles/{articleId}/comments', [CommentController::class, 'index']);

    // ==========================================
    // CATEGORIES (PUBLIC)
    // ==========================================
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);

    // ==========================================
    // AUTHORS (PUBLIC)
    // ==========================================
    Route::get('/authors', [AuthorController::class, 'index']);
    Route::get('/authors/featured', [AuthorController::class, 'featured']);
    Route::get('/authors/top', [AuthorController::class, 'featured']);
    Route::get('/authors/{identifier}', [AuthorController::class, 'show']);
    Route::get('/authors/{identifier}/articles', [AuthorController::class, 'articles']);
    Route::get('/authors/{identifier}/followers', [AuthorController::class, 'followers']);
    Route::get('/authors/{identifier}/following', [AuthorController::class, 'following']);

    // ==========================================
    // SEARCH & AUTOCOMPLETE (PUBLIC)
    // ==========================================
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);

    // ==========================================
    // GENERAL & PLATFORM INFO (PUBLIC)
    // ==========================================
    Route::get('/stats', [GeneralController::class, 'stats']);
    Route::get('/testimonials', [GeneralController::class, 'testimonials']);
    Route::post('/contact', [GeneralController::class, 'contact']);
    Route::post('/newsletter/subscribe', [GeneralController::class, 'subscribeNewsletter']);

    // ==========================================
    // PROTECTED ROUTES (REQUIRES SANCTUM TOKEN)
    // ==========================================
    Route::middleware(['auth:sanctum'])->group(function () {

        // Auth management
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Profile & User settings
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::post('/update', [ProfileController::class, 'update']);
            Route::post('/password', [ProfileController::class, 'changePassword']);
            Route::get('/bookmarks', [ProfileController::class, 'bookmarks']);
            Route::get('/favorites', [ProfileController::class, 'favorites']);
            Route::get('/my-articles', [ProfileController::class, 'myArticles']);
            Route::post('/become-author', [ProfileController::class, 'becomeAuthor']);
        });

        // Article CRUD & User interactions
        Route::post('/articles', [ArticleController::class, 'store']);
        Route::post('/articles/{id}/update', [ArticleController::class, 'update']);
        Route::put('/articles/{id}', [ArticleController::class, 'update']);
        Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);
        Route::post('/articles/{id}/toggle-like', [ArticleController::class, 'toggleLike']);
        Route::post('/articles/{id}/toggle-bookmark', [ArticleController::class, 'toggleBookmark']);

        // Comments management
        Route::post('/articles/{articleId}/comments', [CommentController::class, 'store']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
        Route::post('/comments/{id}/toggle-like', [CommentController::class, 'toggleLike']);

        // Author interactions & follow
        Route::post('/authors/{id}/toggle-follow', [AuthorController::class, 'toggleFollow']);
        Route::post('/authors/{id}/follow', [AuthorController::class, 'follow']);
        Route::post('/authors/{id}/unfollow', [AuthorController::class, 'unfollow']);
        Route::get('/authors/{id}/follow-status', [AuthorController::class, 'followStatus']);

        // Author dashboard & management (for author/admin role)
        Route::prefix('author')->group(function () {
            Route::get('/dashboard', [AuthorController::class, 'dashboard']);
            Route::get('/stats', [AuthorController::class, 'dashboard']);
            Route::get('/comments', [AuthorController::class, 'authorComments']);
            Route::post('/comments/{id}/approve', [AuthorController::class, 'approveComment']);
            Route::delete('/comments/{id}', [AuthorController::class, 'deleteComment']);
            Route::post('/become-author', [ProfileController::class, 'becomeAuthor']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/{id}/read', [NotificationController::class, 'markRead']);
            Route::post('/read-all', [NotificationController::class, 'markAllRead']);
        });
    });
});

// Backward compatibility routes
Route::middleware('web')->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
    Route::get('/posts/feed', [PostController::class, 'feed'])->name('api.posts.feed');
    Route::get('/get/users', [AuthorController::class, 'index']);
    Route::get('/get/users/{id}', [AuthorController::class, 'show']);
});
