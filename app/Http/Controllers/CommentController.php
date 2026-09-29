<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(Request $request): JsonResponse
    {
        // Rate limiting: max 10 comments per minute
        $key = 'comment_' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'error' => 'Too many comments. Please slow down.',
                'retry_after' => RateLimiter::availableIn($key),
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $article = Article::findOrFail($request->input('article_id'));

        // Only allow comments on published articles
        if (!$article->isPublished()) {
            return response()->json(['error' => 'Cannot comment on unpublished articles'], 403);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $article->id,
            'content' => $request->input('content'),
            'status' => 'pending', // Requires moderation
        ]);

        // Update article comment count
        $article->increment('comment_count');

        RateLimiter::hit($key);

        return response()->json([
            'success' => true,
            'message' => 'Comment submitted for moderation.',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Delete a comment.
     */
    public function destroy(int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        // Authorization: only comment owner or admin can delete
        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        // Update article comment count
        $comment->article->decrement('comment_count');

        return response()->json(['success' => true]);
    }

    /**
     * Approve a comment (admin only).
     */
    public function approve(int $id): JsonResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'approved']);

        return response()->json(['success' => true]);
    }

    /**
     * Reject a comment (admin only).
     */
    public function reject(int $id): JsonResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'rejected']);

        return response()->json(['success' => true]);
    }

    /**
     * Toggle like on a comment.
     */
    public function toggleLike(int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        $liked = $user->commentLikes()->where('comment_id', $id)->exists();

        if ($liked) {
            $user->commentLikes()->detach($id);
            $comment->decrement('likes');
            $isLiked = false;
        } else {
            $user->commentLikes()->attach($id);
            $comment->increment('likes');
            $isLiked = true;
        }

        return response()->json([
            'liked' => $isLiked,
            'likes' => $comment->fresh()->likes,
        ]);
    }
}
