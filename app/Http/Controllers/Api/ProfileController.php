<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get authenticated user profile with full statistics.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total_articles' => Article::where('user_id', $user->id)->count(),
            'published_articles' => Article::where('user_id', $user->id)->published()->count(),
            'drafts' => Article::where('user_id', $user->id)->where('status', Article::STATUS_DRAFT)->count(),
            'total_views' => Article::where('user_id', $user->id)->sum('views'),
            'total_likes_received' => Article::where('user_id', $user->id)->sum('likes'),
            'bookmarks_count' => $user->bookmarks()->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->follows()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Update user profile information.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|alpha_dash|max:50|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'tagline' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'social' => 'nullable',
            'avatar' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->only(['name', 'username', 'bio', 'tagline', 'specialty']);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $avatarPath;
        }

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = '/storage/' . $coverPath;
        }

        if ($request->has('social')) {
            $social = $request->input('social');
            $data['social'] = is_array($social) ? json_encode($social) : $social;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Change account password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password does not match.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * Get user's bookmarked articles.
     */
    public function bookmarks(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min(max((int) $request->input('per_page', 10), 1), 30);

        $bookmarks = $user->bookmarks()
            ->with(['user:id,name,username,avatar'])
            ->latest('bookmarks.created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $bookmarks->items(),
            'pagination' => [
                'current_page' => $bookmarks->currentPage(),
                'last_page' => $bookmarks->lastPage(),
                'per_page' => $bookmarks->perPage(),
                'total' => $bookmarks->total(),
            ],
        ]);
    }

    /**
     * Get user's favorite / liked content.
     */
    public function favorites(Request $request): JsonResponse
    {
        $user = $request->user();

        $likedArticles = $user->articleLikes()
            ->with(['user:id,name,username,avatar'])
            ->latest('article_likes.created_at')
            ->limit(20)
            ->get();

        $followedAuthors = $user->follows()
            ->withCount(['articles as published_articles_count' => function ($q) {
                $q->published();
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'liked_articles' => $likedArticles,
                'followed_authors' => $followedAuthors,
            ],
        ]);
    }

    /**
     * Get author's own articles (drafts, pending, published).
     */
    public function myArticles(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->input('status'); // draft, published, pending, or null for all

        $query = Article::where('user_id', $user->id)
            ->withCount('comments')
            ->latest('updated_at');

        if ($status) {
            $query->where('status', $status);
        }

        $perPage = min(max((int) $request->input('per_page', 10), 1), 30);
        $articles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articles->items(),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Request to upgrade account from reader to author.
     */
    public function becomeAuthor(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'author' || $user->role === 'admin') {
            return response()->json([
                'success' => true,
                'message' => 'You are already registered as an author.',
                'role' => $user->role,
            ]);
        }

        $user->update(['role' => 'author']);

        return response()->json([
            'success' => true,
            'message' => 'Your account has been upgraded to Author! You can now publish articles.',
            'role' => 'author',
        ]);
    }
}
