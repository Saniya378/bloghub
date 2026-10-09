<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    /**
     * Chartable metrics, backed by tables that carry a real created_at event
     * timestamp so activity can be bucketed per day.
     *
     * Note: articles.views is a running counter with no per-day snapshot, so
     * views are deliberately excluded from the time series and only reported as
     * a current total.
     */
    public const METRICS = [
        'likes' => [
            'label' => 'Post Likes',
            'table' => 'article_likes',
            'icon' => 'fa-solid fa-heart',
            'color' => '#C8461F',
        ],
        'comments' => [
            'label' => 'Comments',
            'table' => 'comments',
            'icon' => 'fa-solid fa-comment-dots',
            'color' => '#1F2A44',
        ],
        'signups' => [
            'label' => 'New Signups',
            'table' => 'users',
            'icon' => 'fa-solid fa-user-plus',
            'color' => '#198754',
        ],
        'posts' => [
            'label' => 'New Posts',
            'table' => 'articles',
            'icon' => 'fa-solid fa-file-lines',
            'color' => '#B58105',
        ],
        'bookmarks' => [
            'label' => 'Bookmarks',
            'table' => 'bookmarks',
            'icon' => 'fa-solid fa-bookmark',
            'color' => '#0D6EFD',
        ],
        'follows' => [
            'label' => 'New Followers',
            'table' => 'follows',
            'icon' => 'fa-solid fa-user-group',
            'color' => '#6F42C1',
        ],
    ];

    /** How many days a single report may span. */
    private const MAX_DAYS = 366;

    /**
     * Growth analytics over a selectable date range.
     */
    public function index(Request $request): View
    {
        $range = $this->resolveRange($request);

        // Build one series per metric plus the equally long previous period.
        $series = [];
        $totals = [];
        $previousTotals = [];
        $deltas = [];

        foreach (self::METRICS as $key => $meta) {
            $current = $this->dailyCounts($meta['table'], $range['from'], $range['to']);
            $previous = $this->dailyCounts($meta['table'], $range['previous_from'], $range['previous_to']);

            $series[$key] = $current;
            $totals[$key] = array_sum($current);
            $previousTotals[$key] = array_sum($previous);
            $deltas[$key] = $this->percentChange($totals[$key], $previousTotals[$key]);
        }

        return view('pages.admin.reports', [
            'range' => $range,
            'series' => $series,
            'totals' => $totals,
            'previousTotals' => $previousTotals,
            'deltas' => $deltas,
            'dates' => array_keys($series['likes']),
            'snapshot' => $this->platformSnapshot(),
            'growth' => $this->platformGrowth($range),
        ]);
    }

    /**
     * Download the growth report as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $range = $this->resolveRange($request);

        $series = [];
        $totals = [];

        foreach (self::METRICS as $key => $meta) {
            $series[$key] = $this->dailyCounts($meta['table'], $range['from'], $range['to']);
            $totals[$key] = array_sum($series[$key]);
        }

        $dates = array_keys($series['likes']);
        $filename = 'growth-report-' . $range['from']->toDateString() . '-to-' . $range['to']->toDateString() . '.csv';

        return response()->streamDownload(function () use ($dates, $series, $totals, $range) {
            $handle = fopen('php://output', 'w');

            // BOM so Excel reads UTF-8 correctly
            fwrite($handle, "\xEF\xBB\xBF");

            // One header row: Date, then every metric label.
            $header = ['Date'];
            foreach (self::METRICS as $label) {
                $header[] = $label['label'];
            }
            fputcsv($handle, $header);

            foreach ($dates as $date) {
                $row = [$date];
                foreach (array_keys(self::METRICS) as $key) {
                    $row[] = $series[$key][$date] ?? 0;
                }
                fputcsv($handle, $row);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Report', 'Growth Report']);
            fputcsv($handle, ['From', $range['from']->toDateString()]);
            fputcsv($handle, ['To', $range['to']->toDateString()]);
            fputcsv($handle, ['Days', $range['days']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Metric', 'Total']);
            foreach (self::METRICS as $key => $label) {
                fputcsv($handle, [$label['label'], $totals[$key]]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Work out the requested window and the equally long window before it.
     *
     * @return array{
     *     from: Carbon, to: Carbon, previous_from: Carbon, previous_to: Carbon,
     *     days: int, preset: string, label: string
     * }
     */
    private function resolveRange(Request $request): array
    {
        $today = Carbon::today();
        $preset = (string) $request->query('range', '30');

        if ($preset === 'custom') {
            $from = $this->parseDate($request->query('from'), $today->copy()->subDays(29));
            $to = $this->parseDate($request->query('to'), $today->copy());

            if ($to->lt($from)) {
                [$from, $to] = [$to, $from];
            }

            // Keep the query bounded even if someone asks for a decade.
            if ($from->diffInDays($to) + 1 > self::MAX_DAYS) {
                $from = $to->copy()->subDays(self::MAX_DAYS - 1);
            }

            $label = $from->toDateString() . ' → ' . $to->toDateString();
        } else {
            $days = in_array((int) $preset, [7, 30, 90], true) ? (int) $preset : 30;

            $to = $today->copy();
            $from = $today->copy()->subDays($days - 1);

            $label = 'Last ' . $days . ' days';
        }

        $from = $from->copy()->startOfDay();
        $to = $to->copy()->startOfDay();
        $span = $from->diffInDays($to) + 1;

        $previousTo = $from->copy()->subDay();
        $previousFrom = $previousTo->copy()->subDays($span - 1);

        return [
            'from' => $from,
            'to' => $to,
            'previous_from' => $previousFrom,
            'previous_to' => $previousTo,
            'days' => $span,
            'preset' => $preset,
            'label' => $label,
        ];
    }

    /**
     * Parse a user supplied date, falling back when it is missing or invalid.
     */
    private function parseDate(mixed $value, Carbon $fallback): Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return $fallback->copy();
        }

        try {
            return Carbon::parse(trim($value));
        } catch (\Throwable) {
            return $fallback->copy();
        }
    }

    /**
     * Count rows per calendar day between two dates, with empty days as zero.
     *
     * @return array<string, int> date => count
     */
    private function dailyCounts(string $table, Carbon $from, Carbon $to): array
    {
        $rows = DB::table($table)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as bucket, COUNT(*) as total')
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $series = [];

        for ($day = $from->copy()->startOfDay(); $day->lte($to); $day->addDay()) {
            $series[$day->toDateString()] = (int) ($rows[$day->toDateString()] ?? 0);
        }

        return $series;
    }

    /**
     * Percentage change between the current and previous window.
     *
     * Returns null when there is no previous data to compare against, so the
     * view can say "new" instead of inventing a number.
     */
    private function percentChange(int $current, int $previous): ?float
    {
        if ($previous === 0) {
            return $current === 0 ? 0.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Current platform totals (cumulative counters, not period based).
     *
     * @return array<string, int|string>
     */
    private function platformSnapshot(): array
    {
        return [
            'users' => User::count(),
            'authors' => User::where('role', 'author')->count(),
            'readers' => User::where('role', 'reader')->count(),
            'posts' => Article::count(),
            'published' => Article::published()->count(),
            'drafts' => Article::draft()->count(),
            'pending' => Article::pending()->count(),
            'comments' => Comment::count(),
            'approved_comments' => Comment::approved()->count(),
            'pending_comments' => Comment::where('status', 'pending')->count(),
            'views' => (int) Article::sum('views'),
            'likes' => (int) Article::sum('likes'),
            'subscribers' => NewsletterSubscriber::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];
    }

    /**
     * Period based extras that do not fit the metric switcher.
     *
     * @return array<string, int>
     */
    private function platformGrowth(array $range): array
    {
        return [
            // whereDate on both bounds: a between() against a start-of-day end
            // date would drop anything published later that same day.
            'published' => Article::whereDate('published_at', '>=', $range['from']->toDateString())
                ->whereDate('published_at', '<=', $range['to']->toDateString())
                ->count(),
            'pending_comments' => Comment::where('status', 'pending')
                ->whereBetween('created_at', [$range['from'], $range['to']])
                ->count(),
            'subscribers' => NewsletterSubscriber::whereBetween('created_at', [$range['from'], $range['to']])->count(),
            'messages' => ContactMessage::whereBetween('created_at', [$range['from'], $range['to']])->count(),
        ];
    }
}