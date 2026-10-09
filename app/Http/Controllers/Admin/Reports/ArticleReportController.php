<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArticleReportController extends Controller
{
    /**
     * Article Summary Report.
     * Route: /admin/reports/articles
     */
    public function index(Request $request): View
    {
        $query = Article::query()->with(['user', 'reviewer'])->withCount('comments');

        // Dynamic filters as highlighted in PDF Section 8
        $query->when($request->filled('date_from'), function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->input('date_from'));
        });

        $query->when($request->filled('date_to'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->input('date_to'));
        });

        $query->when($request->filled('author_id'), function ($q) use ($request) {
            $q->where('user_id', $request->input('author_id'));
        });

        $query->when($request->filled('category_slug'), function ($q) use ($request) {
            $q->where('category_slug', $request->input('category_slug'));
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->input('status'));
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->input('search');
            $q->where(function ($sq) use ($search) {
                $sq->where('title', 'like', "%{$search}%")
                   ->orWhere('excerpt', 'like', "%{$search}%");
            });
        });

        $articles = $query->latest()->paginate(20)->withQueryString();

        // Article status breakdown
        $totalCount = Article::count();
        $publishedCount = Article::where('status', 'published')->count();
        $pendingCount = Article::where('status', 'pending')->count();
        $rejectedCount = Article::where('status', 'rejected')->count();
        $draftCount = Article::where('status', 'draft')->count();

        $statusStats = [
            'total' => $totalCount,
            'published' => [
                'count' => $publishedCount,
                'share' => $totalCount > 0 ? round(($publishedCount / $totalCount) * 100, 1) : 0,
            ],
            'pending' => [
                'count' => $pendingCount,
                'share' => $totalCount > 0 ? round(($pendingCount / $totalCount) * 100, 1) : 0,
            ],
            'rejected' => [
                'count' => $rejectedCount,
                'share' => $totalCount > 0 ? round(($rejectedCount / $totalCount) * 100, 1) : 0,
            ],
            'draft' => [
                'count' => $draftCount,
                'share' => $totalCount > 0 ? round(($draftCount / $totalCount) * 100, 1) : 0,
            ],
        ];

        $authors = User::where('role', 'author')->orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        return view('pages.admin.reports.articles', compact('articles', 'statusStats', 'authors', 'categories'));
    }

    /**
     * Articles Grouped by Author.
     * Route: /admin/reports/articles/authors
     */
    public function byAuthor(Request $request): View
    {
        $query = User::where('role', 'author')
            ->withCount([
                'articles as total_articles',
                'articles as published_count' => function ($q) { $q->where('status', 'published'); },
                'articles as pending_count' => function ($q) { $q->where('status', 'pending'); },
                'articles as rejected_count' => function ($q) { $q->where('status', 'rejected'); },
                'articles as draft_count' => function ($q) { $q->where('status', 'draft'); },
            ])
            ->withSum('articles as total_views', 'views')
            ->withSum('articles as total_likes', 'likes');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $authors = $query->orderByDesc('total_articles')->paginate(20)->withQueryString();

        return view('pages.admin.reports.articles-by-author', compact('authors'));
    }

    /**
     * Articles Grouped by Category.
     * Route: /admin/reports/articles/categories
     */
    public function byCategory(Request $request): View
    {
        $categories = Category::all();

        $categoryData = [];
        $totalArticlesAll = Article::count();

        foreach ($categories as $cat) {
            $articlesCount = Article::where('category_slug', $cat->slug)->count();
            $publishedCount = Article::where('category_slug', $cat->slug)->where('status', 'published')->count();
            $pendingCount = Article::where('category_slug', $cat->slug)->where('status', 'pending')->count();
            $viewsSum = (int) Article::where('category_slug', $cat->slug)->sum('views');
            $likesSum = (int) Article::where('category_slug', $cat->slug)->sum('likes');

            $categoryData[] = [
                'category' => $cat,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon,
                'total_articles' => $articlesCount,
                'published' => $publishedCount,
                'pending' => $pendingCount,
                'views' => $viewsSum,
                'likes' => $likesSum,
                'share' => $totalArticlesAll > 0 ? round(($articlesCount / $totalArticlesAll) * 100, 1) : 0,
            ];
        }

        // Sort by articles count descending
        usort($categoryData, function ($a, $b) {
            return $b['total_articles'] <=> $a['total_articles'];
        });

        return view('pages.admin.reports.articles-by-category', compact('categoryData', 'totalArticlesAll'));
    }

    /**
     * Export Article Summary CSV.
     */
    public function exportArticles(Request $request): StreamedResponse
    {
        $filename = 'article-summary-report-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Article Summary Report']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['ID', 'Title', 'Author', 'Category', 'Submitted / Created', 'Status', 'Views', 'Likes', 'Comments']);

            $query = Article::with('user');
            if ($request->filled('author_id')) $query->where('user_id', $request->author_id);
            if ($request->filled('category_slug')) $query->where('category_slug', $request->category_slug);
            if ($request->filled('status')) $query->where('status', $request->status);
            if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
            if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

            foreach ($query->latest()->cursor() as $art) {
                fputcsv($handle, [
                    $art->id,
                    $art->title,
                    $art->user?->name ?? 'Unknown',
                    $art->category_slug ?? 'General',
                    $art->submitted_at ? $art->submitted_at->format('Y-m-d H:i') : $art->created_at->format('Y-m-d H:i'),
                    ucfirst($art->status),
                    $art->views,
                    $art->likes,
                    $art->comment_count ?? $art->comments()->count(),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
