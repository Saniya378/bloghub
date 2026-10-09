<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsDashboardController extends Controller
{
    /**
     * Reports Dashboard Landing Page.
     * Route: /admin/reports
     */
    public function index(Request $request): View
    {
        // Summary Cards
        $totalAuthors = User::where('role', 'author')->count();
        $totalArticles = Article::count();
        $totalViews = (int) Article::sum('views');
        $pendingAuthors = User::where('role', 'reader')->count(); // Reader accounts or pending authors
        $pendingArticles = Article::where('status', 'pending')->count();
        $publishedArticles = Article::where('status', 'published')->count();
        $draftArticles = Article::where('status', 'draft')->count();
        $rejectedArticles = Article::where('status', 'rejected')->count();

        // Articles breakdown for chart / status
        $articleBreakdown = [
            'published' => $publishedArticles,
            'pending' => $pendingArticles,
            'rejected' => $rejectedArticles,
            'draft' => $draftArticles,
        ];

        // Authors breakdown
        $activeAuthors = User::where('role', 'author')->where('is_active', true)->count();
        $inactiveAuthors = User::where('role', 'author')->where('is_active', false)->count();
        $authorBreakdown = [
            'approved' => $activeAuthors,
            'pending' => $pendingAuthors,
            'blocked' => $inactiveAuthors,
        ];

        // Monthly Activity (Articles published per month - last 12 months)
        $monthlyArticles = [];
        $monthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->format('M Y');
            $monthLabels[] = $monthLabel;
            $count = Article::where('status', 'published')
                ->whereBetween('published_at', [$monthStart, $monthEnd])
                ->count();
            $monthlyArticles[] = $count;
        }

        // Top Authors ranked by number of published articles
        $topAuthors = User::where('role', 'author')
            ->withCount(['articles' => function ($q) {
                $q->where('status', 'published');
            }])
            ->withSum('articles as total_views', 'views')
            ->orderByDesc('articles_count')
            ->take(5)
            ->get();

        // Top 10 Most Viewed Articles
        $topArticles = Article::with('user')
            ->where('status', 'published')
            ->orderByDesc('views')
            ->take(10)
            ->get();

        return view('pages.admin.reports.index', compact(
            'totalAuthors',
            'totalArticles',
            'totalViews',
            'pendingAuthors',
            'pendingArticles',
            'publishedArticles',
            'draftArticles',
            'rejectedArticles',
            'articleBreakdown',
            'authorBreakdown',
            'monthLabels',
            'monthlyArticles',
            'topAuthors',
            'topArticles'
        ));
    }

    /**
     * Download Dashboard Overview CSV.
     */
    public function export(): StreamedResponse
    {
        $filename = 'reports-dashboard-overview-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM for Excel UTF-8

            fputcsv($handle, ['BlogHub Reports - System Overview Summary']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['Metric', 'Count / Value']);
            fputcsv($handle, ['Total Authors', User::where('role', 'author')->count()]);
            fputcsv($handle, ['Total Articles', Article::count()]);
            fputcsv($handle, ['Published Articles', Article::where('status', 'published')->count()]);
            fputcsv($handle, ['Pending Articles', Article::where('status', 'pending')->count()]);
            fputcsv($handle, ['Rejected Articles', Article::where('status', 'rejected')->count()]);
            fputcsv($handle, ['Draft Articles', Article::where('status', 'draft')->count()]);
            fputcsv($handle, ['Total Views', (int) Article::sum('views')]);
            fputcsv($handle, ['Total Likes', (int) Article::sum('likes')]);
            fputcsv($handle, ['Total Comments', Comment::count()]);

            fputcsv($handle, []);
            fputcsv($handle, ['Top 10 Most Viewed Articles']);
            fputcsv($handle, ['Rank', 'Title', 'Author', 'Category', 'Views', 'Likes', 'Comments', 'Status']);

            $topArticles = Article::with('user')->orderByDesc('views')->take(10)->get();
            foreach ($topArticles as $idx => $art) {
                fputcsv($handle, [
                    $idx + 1,
                    $art->title,
                    $art->user?->name ?? 'Unknown',
                    $art->category_slug ?? 'General',
                    $art->views,
                    $art->likes,
                    $art->comment_count ?? $art->comments()->count(),
                    ucfirst($art->status),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
