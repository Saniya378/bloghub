<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Unified search for articles, authors, and categories.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (empty($q)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'articles' => [],
                    'authors' => [],
                    'categories' => [],
                ],
            ]);
        }

        $perPage = min(max((int) $request->input('per_page', 10), 1), 30);

        $articles = Article::with(['user:id,name,username,avatar'])
            ->published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%")
                    ->orWhereJsonContains('tags', $q);
            })
            ->latest('published_at')
            ->paginate($perPage);

        $authors = User::whereIn('role', ['author', 'admin'])
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('bio', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'username', 'avatar', 'tagline', 'role']);

        $categories = Category::where('name', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'query' => $q,
            'data' => [
                'articles' => $articles->items(),
                'authors' => $authors,
                'categories' => $categories,
            ],
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Fast autocomplete / quick search suggestions.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'suggestions' => []]);
        }

        $articles = Article::published()
            ->where('title', 'like', "%{$q}%")
            ->limit(5)
            ->get(['id', 'title', 'slug', 'category_slug', 'image']);

        $authors = User::whereIn('role', ['author', 'admin'])
            ->where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->limit(3)
            ->get(['id', 'name', 'username', 'avatar']);

        $categories = Category::where('name', 'like', "%{$q}%")
            ->limit(3)
            ->get(['id', 'name', 'slug', 'icon']);

        return response()->json([
            'success' => true,
            'suggestions' => [
                'articles' => $articles,
                'authors' => $authors,
                'categories' => $categories,
            ],
        ]);
    }
}
