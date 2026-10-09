<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityReportController extends Controller
{
    /**
     * Activity / Audit Log Report.
     * Route: /admin/reports/activity
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->input('module'));
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $modules = ['Article', 'Author', 'User', 'Category', 'Comment', 'System'];
        $actions = ['Approved', 'Rejected', 'Submitted', 'Updated', 'Activated', 'Blocked', 'Created', 'Deleted'];

        // Stats summary
        $stats = [
            'total_events' => ActivityLog::count(),
            'approved_events' => ActivityLog::where('action', 'Approved')->count(),
            'rejected_events' => ActivityLog::where('action', 'Rejected')->count(),
            'submitted_events' => ActivityLog::where('action', 'Submitted')->count(),
            'updated_events' => ActivityLog::where('action', 'Updated')->count(),
        ];

        return view('pages.admin.reports.activity', compact('logs', 'users', 'modules', 'actions', 'stats'));
    }

    /**
     * Export Activity Logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $filename = 'activity-audit-log-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Activity / Audit Log Report']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['ID', 'User', 'Email', 'Action', 'Module', 'Description', 'IP Address', 'Date / Time']);

            $query = ActivityLog::with('user');
            if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
            if ($request->filled('action')) $query->where('action', $request->action);
            if ($request->filled('module')) $query->where('module', $request->module);
            if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
            if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

            foreach ($query->latest()->cursor() as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user?->name ?? 'System / Anonymous',
                    $log->user?->email ?? '—',
                    $log->action,
                    $log->module,
                    $log->description,
                    $log->ip_address ?? '—',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
