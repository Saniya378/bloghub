@extends('layouts.dashboard')

@section('title', 'Activity & Audit Log')
@section('report_heading', 'System Activity & Audit Log')
@section('report_subheading', 'Immutable operational trail recording administrative actions, editorial approvals, and account activities.')

@section('report_actions')
<a href="{{ route('admin.reports.activity.export', request()->query()) }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Audit Log CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Activity Stats Row (PDF Section 7) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Audit Events</div>
                    <div class="h3 fw-bold my-1 text-primary" style="font-family: var(--font-meta);">{{ number_format($stats['total_events']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-database me-1"></i>Logged operations</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Approvals</div>
                    <div class="h3 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($stats['approved_events']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-circle-check me-1"></i>Approved actions</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Rejections</div>
                    <div class="h3 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($stats['rejected_events']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-circle-xmark me-1"></i>Rejected actions</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Submissions</div>
                    <div class="h3 fw-bold my-1 text-info" style="font-family: var(--font-meta);">{{ number_format($stats['submitted_events']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-paper-plane me-1"></i>Content submissions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar (PDF Section 7) -->
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.activity') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Search Log</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Description or IP..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Action</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Module</label>
                    <select name="module" class="form-select form-select-sm">
                        <option value="">All Modules</option>
                        @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'user_id', 'action', 'module', 'date_from', 'date_to']))
                    <a href="{{ route('admin.reports.activity') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                    @endif
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Log Table (PDF Section 7) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Audit Trail Log Entries
            </h5>
            <span class="badge bg-light text-dark border">{{ $logs->total() }} Logged Events</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 180px;">User</th>
                        <th style="width: 110px;">Action</th>
                        <th style="width: 110px;">Module</th>
                        <th>Description / Event Detail</th>
                        <th style="width: 130px;">IP Address</th>
                        <th style="width: 170px;" class="text-end">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            @if($log->user)
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $log->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($log->user->name) }}" 
                                     class="rounded-circle" width="28" height="28" alt="{{ $log->user->name }}" style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold text-dark small">{{ $log->user->name }}</div>
                                    <small class="text-muted" style="font-size: 11px;">{{ ucfirst($log->user->role) }}</small>
                                </div>
                            </div>
                            @else
                            <span class="text-muted small"><i class="fa-solid fa-robot me-1"></i>System</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $actionBadge = match(strtolower($log->action)) {
                                    'approved', 'activated', 'created' => 'bg-success-subtle text-success',
                                    'rejected', 'blocked', 'deleted' => 'bg-danger-subtle text-danger',
                                    'submitted' => 'bg-info-subtle text-info',
                                    'updated' => 'bg-warning-subtle text-warning',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <span class="badge {{ $actionBadge }} px-2 py-1">{{ $log->action }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark">{{ $log->description }}</span>
                        </td>
                        <td style="font-family: var(--font-meta); font-size: 0.8125rem;" class="text-muted">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                        <td style="font-family: var(--font-meta); font-size: 0.8125rem;" class="text-end text-muted">
                            <div>{{ $log->created_at->format('d M Y') }}</div>
                            <small class="text-secondary" style="font-size: 11px;">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No activity log entries found for the selected filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-transparent py-3 d-print-none">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
