<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of published articles with filtering.
     */
    public function index(Request $request): View
    {
        $query = Article::with('user')->published();

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

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest('published_at'),
            'popular' => $query->orderByDesc('views'),
            default => $query->latest('published_at'),
        };

        $posts = $query->paginate(9);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data' => $posts->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'excerpt' => $post->excerpt,
                        'category' => $post->category_slug,
                        'author' => $post->user?->name ?? 'Unknown',
                        'date' => $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y'),
                    ];
                }),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
            ]);
        }

        // Only categories that really exist in published articles
        $categories = Article::published()->distinct()->orderBy('category_slug')->pluck('category_slug');

        return view('pages.blogs', compact('posts', 'categories'));
    }

    /**
     * Store a newly created article.
     *
     * Status is limited to draft/pending: publishing always goes
     * through admin review.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'category_slug' => 'required|string|in:technology,education,travel,food,business,sports,health,lifestyle,finance,science,art,culture',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'required|in:draft,pending',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $status = $request->input('status');

        $article = Article::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')) . '-' . Str::random(6),
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
            'category_slug' => $request->input('category_slug'),
            'tags' => $request->input('tags', []),
            'status' => $status,
            'published_at' => null,
            'submitted_at' => $status === Article::STATUS_PENDING ? now() : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'article' => $article], 201);
        }

        return back()->with('success', $status === Article::STATUS_PENDING
            ? 'Post sent to the admin for review.'
            : 'Article saved as draft.');
    }

    /**
     * Update the specified article.
     *
     * An author can never publish here: that only happens when an
     * admin approves the post.
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $article = Article::findOrFail($id);

        // Authorization check
        if ($article->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|required|string|max:500',
            'content' => 'sometimes|required|string',
            'category_slug' => 'sometimes|required|string|in:technology,education,travel,food,business,sports,health,lifestyle,finance,science,art,culture',
            'tags' => 'nullable|array',
            'status' => 'sometimes|in:draft,pending',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $requestedStatus = $data['status'] ?? null;
        unset($data['status']);

        $wasPending = $article->isPending();

        if ($requestedStatus !== null) {
            $data['status'] = $requestedStatus;

            // (Re)submitting for review clears the previous admin decision
            if ($requestedStatus === Article::STATUS_PENDING) {
                $data['submitted_at'] = $wasPending ? $article->submitted_at : now();
                $data['reviewed_at'] = null;
                $data['reviewed_by'] = null;
                $data['review_note'] = null;
            } elseif ($wasPending) {
                // Pulling back to draft withdraws it from the review queue
                $data['reviewed_at'] = null;
                $data['reviewed_by'] = null;
                $data['review_note'] = null;
            }
        }

        $article->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'article' => $article->fresh()]);
        }

        return back()->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified article.
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $article = Article::findOrFail($id);

        // Authorization check
        if ($article->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        $article->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Article deleted successfully.');
    }
}
