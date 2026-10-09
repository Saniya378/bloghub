<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoritesController extends Controller
{
    /**
     * Tabs available on the "My Favourites" page.
     */
    public const TABS = ['posts', 'comments', 'bookmarks', 'following'];

    /**
     * Personal library of everything the signed in user interacted with:
     * liked posts, liked comments, bookmarks and followed authors.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'posts');

        if (!in_array($tab, self::TABS, true)) {
            $tab = 'posts';
        }

        // Only run the query for the tab that is actually open.
        $data = [];

        if ($tab === 'posts') {
            $data['posts'] = $user->articleLikes()
                ->with(['user'])
                ->withPivot('created_at') // belongsToMany only selects the pivot keys by default
                ->published()
                ->orderByDesc('article_likes.created_at')
                ->paginate(9)
                ->withQueryString();
        }

        if ($tab === 'comments') {
            $data['comments'] = $user->commentLikes()
                ->with(['user', 'article:id,slug,title', 'parent' => fn ($q) => $q->with('user:id,name')])
                ->withPivot('created_at')
                ->approved()
                ->orderByDesc('comment_likes.created_at')
                ->paginate(12)
                ->withQueryString();
        }

        if ($tab === 'bookmarks') {
            $data['bookmarks'] = $user->bookmarks()
                ->with(['user'])
                ->withPivot('created_at')
                ->published()
                ->orderByDesc('bookmarks.created_at')
                ->paginate(9)
                ->withQueryString();
        }

        if ($tab === 'following') {
            $data['authors'] = $user->follows()
                ->withCount(['articles', 'followers'])
                ->withPivot('created_at')
                ->orderByDesc('follows.created_at')
                ->paginate(12)
                ->withQueryString();
        }

        $counts = [
            'posts' => $user->articleLikes()->published()->count(),
            'comments' => $user->commentLikes()->approved()->count(),
            'bookmarks' => $user->bookmarks()->published()->count(),
            'following' => $user->follows()->count(),
        ];

        return view('pages.favorites', [
            'tab' => $tab,
            'counts' => $counts,
            'data' => $data,
            'total' => array_sum($counts),
        ]);
    }
}