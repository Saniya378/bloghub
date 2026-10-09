<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Admin overview stats.
     */
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'authors' => User::where('role', 'author')->count(),
            'readers' => User::where('role', 'reader')->count(),
            'posts' => Article::count(),
            'published' => Article::published()->count(),
            'drafts' => Article::draft()->count(),
            'pending_posts' => Article::pending()->count(),
            'pending_comments' => Comment::where('status', 'pending')->count(),
            'subscribers' => NewsletterSubscriber::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
            'testimonials' => Testimonial::count(),
        ];

        return view('pages.admin.overview', compact('stats'));
    }

    /**
     * Manage users.
     */
    public function users(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->latest()->paginate(15);

        return view('pages.admin.users', compact('users'));
    }

    /**
     * Update user role.
     */
    public function updateUserRole(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Cannot change own role
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,author,reader',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user->update(['role' => $request->input('role')]);

        ActivityLog::log('Updated', 'User', "Admin changed role of user {$user->name} to {$request->input('role')}", $user);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    /**
     * Activate/deactivate user.
     */
    public function toggleUserActive(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Cannot deactivate yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        ActivityLog::log($user->is_active ? 'Activated' : 'Blocked', 'User', "Admin {$status} account for {$user->name}", $user);

        return back()->with('success', "User {$user->name} has been {$status}.");
    }

    /**
     * Manage all posts.
     */
    public function posts(Request $request): View
    {
        $query = Article::with(['user', 'reviewer'])->withCount('comments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category_slug', $request->input('category'));
        }

        $posts = $query->latest()->paginate(15);

        return view('pages.admin.posts', compact('posts'));
    }

    /**
     * Approve a post that is waiting for review and publish it.
     */
    public function approvePost(Request $request, int $id): RedirectResponse
    {
        $post = Article::findOrFail($id);

        if (!$post->isPending()) {
            return back()->with('error', "\"{$post->title}\" is not waiting for review.");
        }

        $validator = Validator::make($request->all(), [
            'review_note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $post->approve(auth()->user(), $request->input('review_note'));

        return back()->with('success', "\"{$post->title}\" has been approved and published.");
    }

    /**
     * Reject a post and send it back to the author with a reason.
     */
    public function rejectPost(Request $request, int $id): RedirectResponse
    {
        $post = Article::findOrFail($id);

        if (!$post->isPending()) {
            return back()->with('error', "\"{$post->title}\" is not waiting for review.");
        }

        $validator = Validator::make($request->all(), [
            'review_note' => 'required|string|max:1000',
        ], [
            'review_note.required' => 'Please tell the author why the post was rejected.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $post->reject(auth()->user(), $request->input('review_note'));

        return back()->with('success', "\"{$post->title}\" was rejected and sent back to the author.");
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(int $id): RedirectResponse
    {
        $post = Article::findOrFail($id);
        $post->update(['is_featured' => !$post->is_featured]);

        $status = $post->is_featured ? 'featured' : 'unfeatured';
        ActivityLog::log('Updated', 'Article', "Admin marked post \"{$post->title}\" as {$status}", $post);

        return back()->with('success', "Post \"{$post->title}\" has been {$status}.");
    }

    /**
     * Unpublish post.
     */
    public function unpublish(int $id): RedirectResponse
    {
        $post = Article::findOrFail($id);
        $post->update(['status' => 'draft']);

        ActivityLog::log('Updated', 'Article', "Admin unpublished article \"{$post->title}\"", $post);

        return back()->with('success', "Post \"{$post->title}\" has been unpublished.");
    }

    /**
     * Delete post.
     */
    public function deletePost(int $id): RedirectResponse
    {
        $post = Article::findOrFail($id);
        $title = $post->title;
        $post->delete();

        ActivityLog::log('Deleted', 'Article', "Admin deleted article \"{$title}\"");

        return back()->with('success', "Post \"{$title}\" has been deleted.");
    }

    /**
     * Manage categories.
     */
    public function categories(): View
    {
        $categories = \App\Models\Category::withCount('articles')->orderBy('name')->get();
        return view('pages.admin.categories', compact('categories'));
    }

    /**
     * Store category.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'icon' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = \App\Models\Category::create($validator->validated());
        ActivityLog::log('Created', 'Category', "Admin created category \"{$category->name}\"", $category);

        return back()->with('success', 'Category created successfully.');
    }

    /**
     * Update category.
     */
    public function updateCategory(Request $request, int $id): RedirectResponse
    {
        $category = \App\Models\Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
            'icon' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category->update($validator->validated());
        ActivityLog::log('Updated', 'Category', "Admin updated category \"{$category->name}\"", $category);

        return back()->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category.
     */
    public function deleteCategory(int $id): RedirectResponse
    {
        $category = \App\Models\Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        ActivityLog::log('Deleted', 'Category', "Admin deleted category \"{$name}\"");

        return back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Moderate comments.
     */
    public function comments(Request $request): View
    {
        $query = Comment::with(['user', 'article', 'parent' => fn ($q) => $q->with('user:id,name')])
            ->withCount('replies');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $comments = $query->latest()->paginate(20);

        return view('pages.admin.comments', compact('comments'));
    }

    /**
     * Approve comment.
     */
    public function approveComment(int $id): RedirectResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'approved']);

        ActivityLog::log('Approved', 'Comment', "Admin approved comment on \"{$comment->article?->title}\"", $comment);

        return back()->with('success', 'Comment approved.');
    }

    /**
     * Delete comment, together with every reply nested under it.
     */
    public function deleteComment(int $id): RedirectResponse
    {
        $comment = Comment::findOrFail($id);
        $replies = count($comment->descendantIds());
        $articleTitle = $comment->article?->title ?? 'Post';

        $comment->delete();

        if ($comment->article) {
            $comment->article->decrement('comment_count', $replies + 1);
        }

        ActivityLog::log('Deleted', 'Comment', "Admin deleted comment on \"{$articleTitle}\"");

        return back()->with('success', $replies > 0
            ? "Comment and {$replies} repl" . ($replies === 1 ? 'y' : 'ies') . ' deleted.'
            : 'Comment deleted.');
    }

    /**
     * Manage testimonials.
     */
    public function testimonials(): View
    {
        $testimonials = Testimonial::latest()->paginate(15);
        return view('pages.admin.testimonials', compact('testimonials'));
    }

    /**
     * Store testimonial.
     */
    public function storeTestimonial(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quote' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('testimonials', 'public');
            $data['photo'] = \Illuminate\Support\Facades\Storage::url($path);
        }

        Testimonial::create($data);

        return back()->with('success', 'Testimonial created successfully.');
    }

    /**
     * Update testimonial.
     */
    public function updateTestimonial(Request $request, int $id): RedirectResponse
    {
        $testimonial = Testimonial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quote' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('testimonials', 'public');
            $data['photo'] = \Illuminate\Support\Facades\Storage::url($path);
        }

        $testimonial->update($data);

        return back()->with('success', 'Testimonial updated successfully.');
    }

    /**
     * Delete testimonial.
     */
    public function deleteTestimonial(int $id): RedirectResponse
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted successfully.');
    }

    /**
     * Contact messages.
     */
    public function messages(Request $request): View
    {
        $query = ContactMessage::query();

        if ($request->filled('read')) {
            $query->where('is_read', $request->input('read') === 'read');
        }

        $messages = $query->latest()->paginate(15);

        return view('pages.admin.messages', compact('messages'));
    }

    /**
     * Mark message as read.
     */
    public function markMessageRead(int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);

        return back()->with('success', 'Message marked as read.');
    }

    /**
     * Delete message.
     */
    public function deleteMessage(int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return back()->with('success', 'Message deleted.');
    }

    /**
     * Newsletter subscribers.
     */
    public function subscribers(): View
    {
        $subscribers = NewsletterSubscriber::latest()->paginate(20);
        return view('pages.admin.subscribers', compact('subscribers'));
    }

    /**
     * Export subscribers as CSV.
     */
    public function exportSubscribers(): StreamedResponse
    {
        $subscribers = NewsletterSubscriber::all();

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Subscribed At']);

            foreach ($subscribers as $subscriber) {
                fputcsv($file, [$subscriber->email, $subscriber->created_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subscribers_' . date('Y-m-d') . '.csv"',
        ]);
    }
}
