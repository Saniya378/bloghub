<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show dashboard overview.
     */
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'total_posts' => $user->articles()->count(),
            'published' => $user->articles()->published()->count(),
            'drafts' => $user->articles()->draft()->count(),
            'total_views' => $user->articles()->sum('views'),
            'total_likes' => $user->articles()->sum('likes'),
            'total_comments' => Comment::whereIn('article_id', $user->articles()->pluck('id'))->count(),
            'followers' => $user->followers()->count(),
        ];

        // Views per post for chart
        $postsForChart = $user->articles()
            ->published()
            ->orderByDesc('views')
            ->take(10)
            ->get(['title', 'views']);

        return view('pages.dashboard.overview', compact('stats', 'postsForChart'));
    }

    /**
     * Show my posts table.
     */
    public function posts(Request $request): View
    {
        $query = Auth::user()->articles()->withCount('comments');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $posts = $query->latest()->paginate(10);

        return view('pages.dashboard.posts', compact('posts'));
    }

    /**
     * Show create post form.
     */
    public function create(): View
    {
        return view('pages.dashboard.post-form', ['article' => null]);
    }

    /**
     * Show edit post form.
     */
    public function edit(int $id): View
    {
        $article = Article::findOrFail($id);

        // Policy: authors can only edit their own posts
        if ($article->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You can only edit your own posts.');
        }

        return view('pages.dashboard.post-form', compact('article'));
    }

    /**
     * Store or update post.
     */
    public function store(Request $request, ?int $id = null): RedirectResponse
    {
        $article = $id ? Article::findOrFail($id) : null;

        // Policy check for editing
        if ($article && $article->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You can only edit your own posts.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug,' . ($article?->id ?? 'NULL'),
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'category_slug' => 'required|string|in:technology,education,travel,food,business,sports,health,lifestyle,finance,science,art,culture',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
            'category_slug' => $request->input('category_slug'),
            'tags' => $request->input('tags', []),
            'status' => $request->input('status'),
            'reading_time' => $this->calculateReadingTime($request->input('content')),
        ];

        if ($request->input('status') === 'published' && (!$article || $article->isDraft())) {
            $data['published_at'] = now();
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('featured-images', 'public');
            $data['image'] = Storage::url($path);
        }

        if ($article) {
            $article->update($data);
            $message = 'Post updated successfully.';
        } else {
            $data['user_id'] = Auth::id();
            $article = Article::create($data);
            $message = 'Post created successfully.';
        }

        return redirect('/dashboard/posts')->with('success', $message);
    }

    /**
     * Delete post.
     */
    public function destroy(int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);

        // Policy: authors can only delete their own posts
        if ($article->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You can only delete your own posts.');
        }

        $article->delete();

        return redirect('/dashboard/posts')->with('success', 'Post deleted successfully.');
    }

    /**
     * Show comments on my posts.
     */
    public function comments(Request $request): View
    {
        $query = Comment::with(['user', 'article'])
            ->whereIn('article_id', Auth::user()->articles()->pluck('id'));

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $comments = $query->latest()->paginate(15);

        return view('pages.dashboard.comments', compact('comments'));
    }

    /**
     * Approve comment.
     */
    public function approveComment(int $id): RedirectResponse
    {
        $comment = Comment::findOrFail($id);

        // Policy: only post owner or admin can approve
        if ($comment->article->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You can only moderate comments on your own posts.');
        }

        $comment->update(['status' => 'approved']);

        return back()->with('success', 'Comment approved.');
    }

    /**
     * Delete comment.
     */
    public function deleteComment(int $id): RedirectResponse
    {
        $comment = Comment::findOrFail($id);

        // Policy: only post owner or admin can delete
        if ($comment->article->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You can only moderate comments on your own posts.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }

    /**
     * Calculate reading time from content.
     */
    private function calculateReadingTime(string $content): string
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200);
        return $minutes . ' min read';
    }
}
