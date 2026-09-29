<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Return filtered, paginated posts as JSON.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['user'])
            ->published()
            ->withCount(['comments']);

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_slug', $request->input('category'));
        }

        // Author filter
        if ($request->filled('author')) {
            $query->where('user_id', $request->input('author'));
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest('published_at'),
            'popular' => $query->orderByDesc('views'),
            default => $query->latest('published_at'),
        };

        $posts = $query->paginate(9);

        // Render cards HTML
        $html = '';
        foreach ($posts as $post) {
            $html .= view('components.article-card', ['article' => $post])->render();
        }

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'html' => $html,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'has_more' => $posts->hasMorePages(),
            ],
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', ''),
                'author' => $request->input('author', ''),
                'sort' => $sort,
            ],
        ]);
    }
}
