<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Show the about page with team and stats.
     */
    public function index(): View
    {
        // Team strip: top 4 authors
        $team = User::where('role', 'author')
            ->withCount(['articles' => function ($q) {
                $q->published();
            }])
            ->orderByDesc('articles_count')
            ->take(4)
            ->get();

        // Stats
        $stats = Cache::remember('about_stats', 600, function () {
            return [
                'articles' => Article::published()->count(),
                'authors' => User::where('role', 'author')->count(),
                'categories' => Article::published()->distinct('category_slug')->count(),
                'readers' => User::where('role', 'reader')->count(),
            ];
        });

        return view('pages.about', compact('team', 'stats'));
    }
}
