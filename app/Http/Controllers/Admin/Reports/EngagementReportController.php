<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EngagementReportController extends Controller
{
    /**
     * Engagement & Views Report.
     * Route: /admin/reports/engagement/views
     */
    public function views(Request $request): View
    {
        $timeRange = $request->input('range', 'all');

        $query = Article::with('user')->withCount('comments');

        if ($timeRange === 'today') {
            $query->whereDate('published_at', Carbon::today());
        } elseif ($timeRange === 'week') {
            $query->where('published_at', '>=', Carbon::now()->subDays(7));
        } elseif ($timeRange === 'month') {
            $query->where('published_at', '>=', Carbon::now()->subDays(30));
        } elseif ($timeRange === 'year') {
            $query->where('published_at', '>=', Carbon::now()->subYear());
        } elseif ($timeRange === 'custom') {
            if ($request->filled('date_from')) {
                $query->whereDate('published_at', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('published_at', '<=', $request->input('date_to'));
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // Sorting
        $sort = $request->input('sort', 'views');
        if ($sort === 'likes') {
            $query->orderByDesc('likes');
        } elseif ($sort === 'comments') {
            $query->orderByDesc('comments_count');
        } else {
            $query->orderByDesc('views');
        }

        $articles = $query->paginate(20)->withQueryString();

        // Top 10 Most Popular Articles
        $topArticles = Article::with('user')
            ->withCount('comments')
            ->where('status', 'published')
            ->orderByDesc('views')
            ->take(10)
            ->get();

        // Engagement Summary Totals
        $totals = [
            'total_views' => (int) Article::sum('views'),
            'total_likes' => (int) Article::sum('likes'),
            'total_comments' => Comment::count(),
            'total_bookmarks' => DB::table('bookmarks')->count(),
            'total_follows' => DB::table('follows')->count(),
        ];

        return view('pages.admin.reports.engagement-views', compact('articles', 'topArticles', 'totals', 'timeRange'));
    }

    /**
     * Export Engagement Report to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $filename = 'engagement-views-report-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Engagement & Article Views Report']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['Rank', 'Article Title', 'Author', 'Category', 'Views', 'Likes', 'Comments', 'Status', 'Published Date']);

            $articles = Article::with('user')->withCount('comments')->orderByDesc('views')->get();

            foreach ($articles as $idx => $art) {
                fputcsv($handle, [
                    $idx + 1,
                    $art->title,
                    $art->user?->name ?? 'Unknown',
                    $art->category_slug ?? 'General',
                    $art->views,
                    $art->likes,
                    $art->comments_count,
                    ucfirst($art->status),
                    $art->published_at ? $art->published_at->format('Y-m-d') : 'Draft',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
