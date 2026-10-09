<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuthorReportController extends Controller
{
    /**
     * Author / User Registration Report.
     * Route: /admin/reports/authors
     */
    public function index(Request $request): View
    {
        $query = User::query()->withCount('articles');

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Status / Role filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'blocked' || $status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'author') {
                $query->where('role', 'author');
            } elseif ($status === 'reader') {
                $query->where('role', 'reader');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        // Summary statistics
        $stats = [
            'total_registrations' => User::count(),
            'authors' => User::where('role', 'author')->count(),
            'active_authors' => User::where('role', 'author')->where('is_active', true)->count(),
            'pending_readers' => User::where('role', 'reader')->count(),
            'blocked' => User::where('is_active', false)->count(),
        ];

        return view('pages.admin.reports.authors', compact('users', 'stats'));
    }

    /**
     * Author Activity Report.
     * Route: /admin/reports/authors/activity
     */
    public function activity(Request $request): View
    {
        $query = User::where('role', 'author')
            ->withCount([
                'articles as submitted_count',
                'articles as published_count' => function ($q) {
                    $q->where('status', 'published');
                },
                'articles as pending_count' => function ($q) {
                    $q->where('status', 'pending');
                },
                'articles as rejected_count' => function ($q) {
                    $q->where('status', 'rejected');
                },
                'articles as draft_count' => function ($q) {
                    $q->where('status', 'draft');
                },
            ])
            ->withSum('articles as total_views', 'views')
            ->withSum('articles as total_likes', 'likes');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'published');
        if ($sort === 'views') {
            $query->orderByDesc('total_views');
        } elseif ($sort === 'submitted') {
            $query->orderByDesc('submitted_count');
        } elseif ($sort === 'pending') {
            $query->orderByDesc('pending_count');
        } else {
            $query->orderByDesc('published_count');
        }

        $authors = $query->paginate(20)->withQueryString();

        // Productivity totals
        $totals = [
            'total_authors' => User::where('role', 'author')->count(),
            'total_submitted' => (int) \App\Models\Article::count(),
            'total_published' => (int) \App\Models\Article::where('status', 'published')->count(),
            'total_pending' => (int) \App\Models\Article::where('status', 'pending')->count(),
            'total_rejected' => (int) \App\Models\Article::where('status', 'rejected')->count(),
            'total_views' => (int) \App\Models\Article::sum('views'),
        ];

        return view('pages.admin.reports.author-activity', compact('authors', 'totals'));
    }

    /**
     * Export Author Registrations to CSV.
     */
    public function exportRegistrations(Request $request): StreamedResponse
    {
        $filename = 'author-registrations-report-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Author / User Registration Report']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Status', 'Registered Date', 'Articles Count']);

            $query = User::withCount('articles');
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }
            if ($request->filled('status')) {
                $status = $request->input('status');
                if ($status === 'active') $query->where('is_active', true);
                elseif ($status === 'blocked') $query->where('is_active', false);
                elseif ($status === 'author') $query->where('role', 'author');
                elseif ($status === 'reader') $query->where('role', 'reader');
            }

            foreach ($query->latest()->cursor() as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    ucfirst($user->role),
                    $user->is_active ? 'Active' : 'Blocked',
                    $user->created_at->format('Y-m-d H:i'),
                    $user->articles_count,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Export Author Activity to CSV.
     */
    public function exportActivity(): StreamedResponse
    {
        $filename = 'author-activity-report-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Author Productivity & Activity Report']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['Rank', 'Author Name', 'Email', 'Submitted', 'Published', 'Pending', 'Rejected', 'Draft', 'Total Views', 'Total Likes']);

            $authors = User::where('role', 'author')
                ->withCount([
                    'articles as submitted_count',
                    'articles as published_count' => function ($q) { $q->where('status', 'published'); },
                    'articles as pending_count' => function ($q) { $q->where('status', 'pending'); },
                    'articles as rejected_count' => function ($q) { $q->where('status', 'rejected'); },
                    'articles as draft_count' => function ($q) { $q->where('status', 'draft'); },
                ])
                ->withSum('articles as total_views', 'views')
                ->withSum('articles as total_likes', 'likes')
                ->orderByDesc('published_count')
                ->get();

            foreach ($authors as $idx => $author) {
                fputcsv($handle, [
                    $idx + 1,
                    $author->name,
                    $author->email,
                    $author->submitted_count,
                    $author->published_count,
                    $author->pending_count,
                    $author->rejected_count,
                    $author->draft_count,
                    $author->total_views ?? 0,
                    $author->total_likes ?? 0,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
