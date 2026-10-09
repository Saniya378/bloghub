<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store a newly created comment (top level or a reply to another comment).
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
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $article = Article::findOrFail($request->input('article_id'));

        // Only allow comments on published articles
        if (!$article->isPublished()) {
            return response()->json(['error' => 'Cannot comment on unpublished articles'], 403);
        }

        $parent = null;
        if ($request->filled('parent_id')) {
            $parent = Comment::findOrFail($request->input('parent_id'));

            // A reply must belong to the same article thread
            if ($parent->article_id !== $article->id) {
                return response()->json(['error' => 'Invalid parent comment'], 422);
            }

            // Never reply to a comment that is not visible yet
            if (!$parent->isApproved()) {
                return response()->json(['error' => 'You cannot reply to this comment'], 422);
            }
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $article->id,
            'parent_id' => $parent?->id,
            'content' => $request->input('content'),
            'status' => 'pending', // Requires moderation
        ]);

        $comment->load('user');

        // Update article comment count
        $article->increment('comment_count');

        RateLimiter::hit($key);

        return response()->json([
            'success' => true,
            'message' => $parent
                ? 'Reply submitted for moderation.'
                : 'Comment submitted for moderation.',
            'comment' => $comment,
            'is_reply' => (bool) $parent,
        ], 201);
    }

    /**
     * Render the comment thread for an article as an HTML fragment.
     *
     * Used to refresh the thread after posting a comment or reply without a
     * full page reload.
     */
    public function thread(int $articleId): JsonResponse
    {
        $article = Article::findOrFail($articleId);

        // Load the whole approved tree in one query, then group replies in PHP.
        $tree = Comment::approved()
            ->with(['user', 'likers', 'article:id,user_id'])
            ->where('article_id', $article->id)
            ->oldest()
            ->get();

        $repliesByParent = [];
        foreach ($tree as $comment) {
            if ($comment->parent_id !== null) {
                $repliesByParent[$comment->parent_id][] = $comment;
            }
        }

        // Only top level comments render at the root; replies render inside their parent.
        $roots = $tree->whereNull('parent_id')->values();

        $html = view('partials.comment-thread', [
            'comments' => $roots,
            'repliesByParent' => $repliesByParent,
        ])->render();

        return response()->json([
            'html' => $html,
            'total' => $tree->count(),
            'comment_count' => $article->fresh()->comment_count,
        ]);
    }

    /**
     * Delete a comment together with all of its replies.
     */
    public function destroy(int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        // Authorization: only comment owner, post owner or admin can delete
        $canDelete = Auth::id() === $comment->user_id
            || Auth::id() === $comment->article?->user_id
            || Auth::user()->hasRole('admin');

        if (!$canDelete) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $replyCount = count($comment->descendantIds());

        $comment->delete();

        // Update article comment count (this comment plus every reply removed)
        if ($comment->article) {
            $comment->article->decrement('comment_count', $replyCount + 1);
        }

        return response()->json([
            'success' => true,
            'removed' => $replyCount + 1,
        ]);
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

        $likes = max(0, $comment->fresh()->likes);

        return response()->json([
            'liked' => $isLiked,
            'likes' => $likes,
        ]);
    }
}