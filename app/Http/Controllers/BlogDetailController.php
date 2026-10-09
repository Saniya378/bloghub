<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogDetailController extends Controller
{
    /**
     * Show a single blog post by slug.
     */
    public function show(string $slug, Request $request): View
    {
        $article = Article::with('user')
            ->where('slug', $slug)
            ->first();

        // Custom 404 for missing slugs
        if (!$article) {
            abort(404, 'Article not found');
        }

        // Draft visibility: only owner or admin can view
        if ($article->isDraft()) {
            $user = $request->user();
            if (!$user || ($user->id !== $article->user_id && !$user->hasRole('admin'))) {
                abort(404, 'Article not found');
            }
        }

        // Only published posts are public
        if (!$article->isPublished()) {
            $user = $request->user();
            if (!$user || ($user->id !== $article->user_id && !$user->hasRole('admin'))) {
                abort(404, 'Article not found');
            }
        }

        // Increment views once per session per post
        $viewedKey = 'viewed_article_' . $article->id;
        if (!$request->session()->has($viewedKey)) {
            $article->increment('views');
            $request->session()->put($viewedKey, true);
        }

        // Related posts (same category, exclude current, max 4)
        $related = Article::with('user')
            ->published()
            ->where('category_slug', $article->category_slug)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(4)
            ->get();

        // Approved comment tree, grouped so replies render inside their parent
        $tree = Comment::approved()
            ->with(['user', 'likers', 'article:id,user_id'])
            ->where('article_id', $article->id)
            ->oldest()
            ->get();

        $commentReplies = [];
        foreach ($tree as $comment) {
            if ($comment->parent_id !== null) {
                $commentReplies[$comment->parent_id][] = $comment;
            }
        }

        $commentRoots = $tree->whereNull('parent_id')->values();
        $commentTotal = $tree->count();

        return view('pages.blog-details', compact('article', 'related', 'commentRoots', 'commentReplies', 'commentTotal'));
    }
}
