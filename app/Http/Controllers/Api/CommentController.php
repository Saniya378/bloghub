<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Get nested comments list for an article.
     */
    public function index(int $articleId): JsonResponse
    {
        $article = Article::find($articleId);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $comments = Comment::where('article_id', $articleId)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->with([
                'user:id,name,username,avatar,role',
                'replies' => function ($q) {
                    $q->where('status', 'approved')
                        ->with(['user:id,name,username,avatar,role'])
                        ->latest();
                },
            ])
            ->latest()
            ->get();

        // Check if user liked each comment
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $likedCommentIds = $user->commentLikes()->pluck('comment_id')->toArray();
            
            $comments->transform(function ($comment) use ($likedCommentIds) {
                $comment->is_liked = in_array($comment->id, $likedCommentIds, true);
                if ($comment->replies) {
                    $comment->replies->transform(function ($reply) use ($likedCommentIds) {
                        $reply->is_liked = in_array($reply->id, $likedCommentIds, true);
                        return $reply;
                    });
                }
                return $comment;
            });
        }

        return response()->json([
            'success' => true,
            'count' => $article->comments()->where('status', 'approved')->count(),
            'data' => $comments,
        ]);
    }

    /**
     * Post a new comment or reply to an article.
     */
    public function store(Request $request, int $articleId): JsonResponse
    {
        $article = Article::find($articleId);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Auto-approve or moderate based on role:
        // Readers require moderation (status: pending).
        // Authors and admins are auto-approved (status: approved).
        $status = ($user->role === 'admin' || $user->role === 'author') ? 'approved' : 'pending';

        $comment = Comment::create([
            'user_id' => $user->id,
            'article_id' => $articleId,
            'parent_id' => $request->input('parent_id'),
            'content' => trim($request->input('content')),
            'status' => $status,
            'likes' => 0,
        ]);

        if ($status === 'approved') {
            $article->increment('comment_count');
        }

        return response()->json([
            'success' => true,
            'message' => $status === 'approved' ? 'Comment posted successfully.' : 'Comment submitted for moderation.',
            'data' => $comment->load('user:id,name,username,avatar,role'),
        ], 201);
    }

    /**
     * Delete a comment (owner, article author, or admin).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $comment = Comment::with('article')->find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        $user = $request->user();
        $isArticleAuthor = $comment->article && $comment->article->user_id === $user->id;

        if ($comment->user_id !== $user->id && !$isArticleAuthor && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this comment.',
            ], 403);
        }

        $wasApproved = $comment->status === 'approved';
        $replyCount = count($comment->descendantIds());
        $article = $comment->article;
        $comment->delete();

        if ($article && $wasApproved && $article->comment_count > 0) {
            $article->decrement('comment_count', min($article->comment_count, $replyCount + 1));
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully.',
        ]);
    }

    /**
     * Toggle like on a comment.
     */
    public function toggleLike(Request $request, int $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        $user = $request->user();
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
            'success' => true,
            'liked' => $isLiked,
            'likes_count' => $comment->fresh()->likes,
        ]);
    }
}
