<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BlogDetailController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ToggleController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->middleware('throttle:5,1');

Route::get('/ui-kit', function () {
    return response()->view('pages.ui-kit')
        ->header('Cache-Control', 'public, max-age=300');
});

Route::get('/blogs', [ArticleController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [App\Http\Controllers\BlogDetailController::class, 'show'])->name('blogs.show');

// Comment thread fragment, public so guests see live replies and likes too
Route::get('/comments/{articleId}/thread', [CommentController::class, 'thread'])->name('comments.thread');

Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
Route::get('/search-results', [App\Http\Controllers\SearchController::class, 'index']);
Route::get('/api/search/suggestions', [App\Http\Controllers\SearchController::class, 'suggestions']);
Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::get('/authors', [App\Http\Controllers\AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{username}', [App\Http\Controllers\AuthorController::class, 'show'])->name('authors.show');

Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('/contact', function () {
    return response()->view('pages.contact')
        ->header('Cache-Control', 'public, max-age=300');
});
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store']);
Route::get('/privacy', function () {
    return response()->view('pages.privacy')
        ->header('Cache-Control', 'public, max-age=3600');
});
Route::get('/terms', function () {
    return response()->view('pages.terms')
        ->header('Cache-Control', 'public, max-age=3600');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// OTP / Password Reset routes
Route::get('/forgot-password', function () {
    return view('pages.forgot-password');
})->name('password.request');
Route::post('/forgot-password', [OtpController::class, 'requestOtp'])->middleware('throttle:3,1');

Route::get('/verify-otp', function () {
    return view('pages.verify-otp');
})->name('password.verify');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);

Route::get('/reset-password', function () {
    return view('pages.reset-password');
})->name('password.reset');
Route::post('/reset-password', [OtpController::class, 'resetPassword']);

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);
    Route::post('/become-author', [ProfileController::class, 'becomeAuthor']);
    Route::get('/bookmarks', [ProfileController::class, 'bookmarks']);

    // Favourites dashboard (favourited posts, liked comments, bookmarks, following)
    Route::get('/favorites', [FavoritesController::class, 'index'])->name('favorites.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    
    // Articles
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);
    
    // Comments
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::post('/comments/{id}/like', [CommentController::class, 'toggleLike']);
    
    // Toggles
    Route::post('/articles/{id}/like', [ToggleController::class, 'toggleArticleLike']);
    Route::post('/articles/{id}/bookmark', [ToggleController::class, 'toggleBookmark']);
    Route::post('/authors/{id}/follow', [ToggleController::class, 'toggleFollow']);
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('index');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::post('/users/{id}/role', [App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('users.role');
    Route::post('/users/{id}/toggle', [App\Http\Controllers\AdminController::class, 'toggleUserActive'])->name('users.toggle');
    Route::get('/posts', [App\Http\Controllers\AdminController::class, 'posts'])->name('posts');
    Route::post('/posts/{id}/approve', [App\Http\Controllers\AdminController::class, 'approvePost'])->name('posts.approve');
    Route::post('/posts/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectPost'])->name('posts.reject');
    Route::post('/posts/{id}/featured', [App\Http\Controllers\AdminController::class, 'toggleFeatured'])->name('posts.featured');
    Route::post('/posts/{id}/unpublish', [App\Http\Controllers\AdminController::class, 'unpublish'])->name('posts.unpublish');
    Route::post('/posts/{id}/delete', [App\Http\Controllers\AdminController::class, 'deletePost'])->name('posts.delete');
    Route::get('/categories', [App\Http\Controllers\AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [App\Http\Controllers\AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}', [App\Http\Controllers\AdminController::class, 'updateCategory'])->name('categories.update');
    Route::post('/categories/{id}/delete', [App\Http\Controllers\AdminController::class, 'deleteCategory'])->name('categories.delete');
    Route::get('/comments', [App\Http\Controllers\AdminController::class, 'comments'])->name('comments');
    Route::post('/comments/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveComment'])->name('comments.approve');
    Route::post('/comments/{id}/delete', [App\Http\Controllers\AdminController::class, 'deleteComment'])->name('comments.delete');
    Route::get('/testimonials', [App\Http\Controllers\AdminController::class, 'testimonials'])->name('testimonials');
    Route::post('/testimonials', [App\Http\Controllers\AdminController::class, 'storeTestimonial'])->name('testimonials.store');
    Route::put('/testimonials/{id}', [App\Http\Controllers\AdminController::class, 'updateTestimonial'])->name('testimonials.update');
    Route::post('/testimonials/{id}/delete', [App\Http\Controllers\AdminController::class, 'deleteTestimonial'])->name('testimonials.delete');
    Route::get('/messages', [App\Http\Controllers\AdminController::class, 'messages'])->name('messages');
    Route::post('/messages/{id}/read', [App\Http\Controllers\AdminController::class, 'markMessageRead'])->name('messages.read');
    Route::post('/messages/{id}/delete', [App\Http\Controllers\AdminController::class, 'deleteMessage'])->name('messages.delete');
    Route::get('/subscribers', [App\Http\Controllers\AdminController::class, 'subscribers'])->name('subscribers');
    Route::get('/subscribers/export', [App\Http\Controllers\AdminController::class, 'exportSubscribers'])->name('subscribers.export');

    // Reports Module (PDF Specification)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\Reports\ReportsDashboardController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\Reports\ReportsDashboardController::class, 'export'])->name('export');

        // Authors / Users
        Route::get('/authors', [\App\Http\Controllers\Admin\Reports\AuthorReportController::class, 'index'])->name('authors');
        Route::get('/authors/activity', [\App\Http\Controllers\Admin\Reports\AuthorReportController::class, 'activity'])->name('authors.activity');
        Route::get('/authors/export/registrations', [\App\Http\Controllers\Admin\Reports\AuthorReportController::class, 'exportRegistrations'])->name('authors.export.registrations');
        Route::get('/authors/export/activity', [\App\Http\Controllers\Admin\Reports\AuthorReportController::class, 'exportActivity'])->name('authors.export.activity');

        // Articles
        Route::get('/articles', [\App\Http\Controllers\Admin\Reports\ArticleReportController::class, 'index'])->name('articles');
        Route::get('/articles/authors', [\App\Http\Controllers\Admin\Reports\ArticleReportController::class, 'byAuthor'])->name('articles.authors');
        Route::get('/articles/categories', [\App\Http\Controllers\Admin\Reports\ArticleReportController::class, 'byCategory'])->name('articles.categories');
        Route::get('/articles/export', [\App\Http\Controllers\Admin\Reports\ArticleReportController::class, 'exportArticles'])->name('articles.export');

        // Approvals
        Route::get('/approvals/authors', [\App\Http\Controllers\Admin\Reports\ApprovalReportController::class, 'authors'])->name('approvals.authors');
        Route::get('/approvals/articles', [\App\Http\Controllers\Admin\Reports\ApprovalReportController::class, 'articles'])->name('approvals.articles');
        Route::get('/approvals/articles/export', [\App\Http\Controllers\Admin\Reports\ApprovalReportController::class, 'exportArticleApprovals'])->name('approvals.articles.export');

        // Engagement
        Route::get('/engagement/views', [\App\Http\Controllers\Admin\Reports\EngagementReportController::class, 'views'])->name('engagement.views');
        Route::get('/engagement/export', [\App\Http\Controllers\Admin\Reports\EngagementReportController::class, 'export'])->name('engagement.export');

        // Activity Log
        Route::get('/activity', [\App\Http\Controllers\Admin\Reports\ActivityReportController::class, 'index'])->name('activity');
        Route::get('/activity/export', [\App\Http\Controllers\Admin\Reports\ActivityReportController::class, 'export'])->name('activity.export');
    });
    // Alias for old admin.reports
    Route::get('/reports-legacy', [\App\Http\Controllers\Admin\Reports\ReportsDashboardController::class, 'index'])->name('reports');
});

// GET /dashboard: admins are redirected to the admin panel, only authors see the author dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->role !== 'author') {
        return redirect(auth()->user()->dashboardUrl());
    }

    return app(DashboardController::class)->index();
})->middleware(['auth'])->name('dashboard');

// Author routes
Route::middleware(['auth', 'role:author'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/posts', [App\Http\Controllers\DashboardController::class, 'posts'])->name('posts');
    Route::get('/posts/create', [App\Http\Controllers\DashboardController::class, 'create'])->name('posts.create');
    Route::get('/posts/{id}/edit', [App\Http\Controllers\DashboardController::class, 'edit'])->name('posts.edit');
    Route::post('/posts', [App\Http\Controllers\DashboardController::class, 'store'])->name('posts.store');
    Route::put('/posts/{id}', [App\Http\Controllers\DashboardController::class, 'store'])->name('posts.update');
    Route::delete('/posts/{id}', [App\Http\Controllers\DashboardController::class, 'destroy'])->name('posts.destroy');
    Route::get('/comments', [App\Http\Controllers\DashboardController::class, 'comments'])->name('comments');
    Route::post('/comments/{id}/approve', [App\Http\Controllers\DashboardController::class, 'approveComment'])->name('comments.approve');
    Route::delete('/comments/{id}', [App\Http\Controllers\DashboardController::class, 'deleteComment'])->name('comments.delete');
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404)
        ->header('Cache-Control', 'public, max-age=60');
});
