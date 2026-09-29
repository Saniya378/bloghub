<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\NewsletterSubscriber;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the home page with database-driven content.
     */
    public function index(): View
    {
        // Featured articles (asymmetric grid: 1 large + 2 compact)
        $featured = Article::with('user')
            ->published()
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        // Trending posts (by views in last 30 days, backfilled with all-time most viewed)
        $trending = Article::with('user')
            ->published()
            ->where('published_at', '>=', now()->subDays(30))
            ->orderBy('views', 'desc')
            ->take(6)
            ->get();

        if ($trending->count() < 6) {
            $trending = $trending->merge(
                Article::with('user')
                    ->published()
                    ->whereNotIn('id', $trending->pluck('id'))
                    ->orderBy('views', 'desc')
                    ->take(6 - $trending->count())
                    ->get()
            );
        }

        // Latest posts (uniform grid)
        $latest = Article::with('user')
            ->published()
            ->latest()
            ->take(6)
            ->get();

        // NOTE: The blocks below are cached — run `php artisan cache:clear` after reseeding.

        // Popular categories with real article counts
        $categories = Cache::remember('home_categories', 600, function () {
            return Article::published()
                ->select('category_slug', \DB::raw('count(*) as count'))
                ->groupBy('category_slug')
                ->orderByDesc('count')
                ->take(10)
                ->pluck('count', 'category_slug')
                ->toArray();
        });

        // Featured authors with real article counts
        $authors = Cache::remember('home_authors', 600, function () {
            return User::where('role', 'author')
                ->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->orderByDesc('articles_count')
                ->take(6)
                ->get();
        });

        // Statistics band (cached for 10 minutes)
        $stats = Cache::remember('home_stats', 600, function () {
            return [
                'articles' => Article::published()->count(),
                'authors' => User::where('role', 'author')->count(),
                'categories' => Article::published()->distinct()->count('category_slug'),
                'readers' => User::where('role', 'reader')->count(),
            ];
        });

        // Testimonials from DB
        $testimonials = Testimonial::orderBy('rating', 'desc')
            ->take(10)
            ->get();

        return view('pages.home', compact(
            'featured',
            'trending',
            'latest',
            'categories',
            'authors',
            'stats',
            'testimonials'
        ));
    }

    /**
     * Handle newsletter subscription via fetch POST.
     */
    public function subscribeNewsletter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.',
            ], 422);
        }

        $email = $request->input('email');

        // Check for duplicate
        $exists = NewsletterSubscriber::where('email', $email)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already subscribed.',
            ], 409);
        }

        NewsletterSubscriber::create(['email' => $email]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing!',
        ]);
    }
}
