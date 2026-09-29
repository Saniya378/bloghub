<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * Show authors directory with real article counts.
     */
    public function index(Request $request): View
    {
        $query = $request->input('q', '');

        $authors = User::where('role', 'author')
            ->withCount(['articles' => function ($q) {
                $q->published();
            }])
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('specialty', 'like', "%{$query}%")
                    ->orWhere('tagline', 'like', "%{$query}%");
            })
            ->orderByDesc('articles_count')
            ->get();

        return view('pages.authors', compact('authors', 'query'));
    }

    /**
     * Show author profile by username.
     */
    public function show(string $username): View
    {
        $author = User::where('role', 'author')
            ->where('username', $username)
            ->withCount(['articles' => function ($q) {
                $q->published();
            }])
            ->firstOrFail();

        $posts = Article::with('user')
            ->published()
            ->where('user_id', $author->id)
            ->latest()
            ->paginate(9);

        // Topic tags from author's article categories
        $topicTags = Article::published()
            ->where('user_id', $author->id)
            ->distinct()
            ->pluck('category_slug')
            ->toArray();

        // Stats
        $stats = [
            'articles' => $author->articles_count,
            'followers' => $author->followers()->count(),
            'following' => $author->following()->count(),
            'total_views' => Article::published()->where('user_id', $author->id)->sum('views'),
        ];

        return view('pages.author-profile', compact('author', 'posts', 'topicTags', 'stats'));
    }
}
