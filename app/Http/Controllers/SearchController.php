<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Show search results page.
     */
    public function index(Request $request): View
    {
        $query = $request->input('q', '');
        $results = [];

        if ($query) {
            $results = Article::with('user')
                ->published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('excerpt', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%")
                        ->orWhereJsonContains('tags', $query);
                })
                ->latest()
                ->get();
        }

        // Suggested categories for empty state
        $suggestedCategories = Article::published()
            ->select('category_slug', \DB::raw('count(*) as count'))
            ->groupBy('category_slug')
            ->orderByDesc('count')
            ->take(6)
            ->pluck('category_slug')
            ->toArray();

        return view('pages.search-results', compact('query', 'results', 'suggestedCategories'));
    }

    /**
     * Live search suggestions for navbar overlay.
     */
    public function suggestions(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = Article::published()
            ->where('title', 'like', "%{$query}%")
            ->orWhere('excerpt', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'slug', 'category_slug'])
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'url' => '/blogs/' . $article->slug,
                    'category' => $article->category_slug,
                ];
            });

        return response()->json(['suggestions' => $suggestions]);
    }
}
