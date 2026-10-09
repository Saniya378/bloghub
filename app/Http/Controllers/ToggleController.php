<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ToggleController extends Controller
{
    /**
     * Toggle like on an article.
     */
    public function toggleArticleLike(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $user = Auth::user();

        $liked = $user->articleLikes()->where('article_id', $id)->exists();

        if ($liked) {
            $user->articleLikes()->detach($id);
            $article->decrement('likes');
            $isLiked = false;
        } else {
            $user->articleLikes()->attach($id);
            $article->increment('likes');
            $isLiked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $isLiked,
            'likes' => $article->fresh()->likes,
        ]);
    }

    /**
     * Toggle bookmark on an article.
     */
    public function toggleBookmark(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $user = Auth::user();

        $bookmarked = $user->bookmarks()->where('article_id', $id)->exists();

        if ($bookmarked) {
            $user->bookmarks()->detach($id);
            $isBookmarked = false;
        } else {
            $user->bookmarks()->attach($id);
            $isBookmarked = true;
        }

        return response()->json([
            'success' => true,
            'bookmarked' => $isBookmarked,
        ]);
    }

    /**
     * Toggle follow on an author.
     */
    public function toggleFollow(int $id): JsonResponse
    {
        $author = \App\Models\User::findOrFail($id);
        $user = Auth::user();

        // Prevent self-following
        if ($user->id === $author->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 400);
        }

        $following = $user->follows()->where('author_id', $id)->exists();

        if ($following) {
            $user->follows()->detach($id);
            $isFollowing = false;
        } else {
            $user->follows()->attach($id);
            $isFollowing = true;
        }

        return response()->json([
            'success' => true,
            'following' => $isFollowing,
            'followers' => $author->followers()->count(),
        ]);
    }
}
