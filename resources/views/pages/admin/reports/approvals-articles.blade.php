@extends('layouts.dashboard')

@section('title', 'Article Approval Report')
@section('report_heading', 'Article Approvals & Editorial Moderation')
@section('report_subheading', 'Tracking review velocity, editorial turnaround times, approvals, and rejection feedback.')

@section('report_actions')
<a href="{{ route('admin.reports.approvals.articles.export', request()->query()) }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Approvals CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Useful Metrics / Turnaround Times (PDF Section 5) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Avg Article Turnaround</div>
                    <div class="h4 fw-bold my-1 text-info" style="font-family: var(--font-meta);">{{ $stats['avg_article_approval_time'] }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-stopwatch me-1"></i>Submission to Decision</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Approved & Published</div>
                    <div class="h4 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($stats['approved_count']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-circle-check me-1"></i>Accepted Articles</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Rejected / Revisions</div>
                    <div class="h4 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($stats['rejected_count']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-triangle-exclamation me-1"></i>Returned with notes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Waiting in Queue</div>
                    <div class="h4 fw-bold my-1 text-warning" style="font-family: var(--font-meta);">{{ number_format($stats['pending_review']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-hourglass-start me-1"></i>Needs Admin Review</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.approvals.articles') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold mb-1">Search Article Title</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search title..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Review Decision</label>
                    <select name="decision" class="form-select form-select-sm">
                        <option value="">All Decisions</option>
                        <option value="approved" {{ request('decision') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('decision') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="pending" {{ request('decision') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'decision', 'date_from', 'date_to']))
                    <a href="{{ route('admin.reports.approvals.articles') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Approval History Table (PDF Section 5.B) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-clipboard-check text-success me-2"></i>Editorial Review Decisions
            </h5>
            <span class="badge bg-light text-dark border">{{ $articles->total() }} Records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Article</th>
                        <th>Author</th>
                        <th>Submitted</th>
                        <th>Reviewed</th>
                        <th>Decision</th>
                        <th>Reviewed By</th>
                        <th>Reviewer Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $art)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $art->title }}</div>
                            <small class="text-muted">{{ $art->category_slug ?? 'General' }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $art->user?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($art->user?->name ?? 'User') }}" 
                                     class="rounded-circle" width="28" height="28" alt="{{ $art->user?->name }}" style="object-fit: cover;">
                                <span class="fw-medium text-dark small">{{ $art->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td style="font-family: var(--font-meta); font-size: 0.8125rem;">
                            {{ $art->submitted_at ? $art->submitted_at->format('d M, H:i') : $art->created_at->format('d M, H:i') }}
                        </td>
                        <td style="font-family: var(--font-meta); font-size: 0.8125rem;">
                            {{ $art->reviewed_at ? $art->reviewed_at->format('d M, H:i') : ($art->status === 'published' ? $art->created_at->format('d M, H:i') : 'Pending') }}
                        </td>
                        <td>
                            @if($art->status === 'published')
                                <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fa-solid fa-check me-1"></i>Approved</span>
                            @elseif($art->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="fa-solid fa-xmark me-1"></i>Rejected</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning px-2 py-1"><i class="fa-solid fa-clock me-1"></i>Pending</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="fa-solid fa-user-shield me-1 text-primary"></i>{{ $art->reviewer?->name ?? 'Editorial Staff' }}
                            </span>
                        </td>
                        <td>
                            @if($art->review_note)
                            <span class="text-muted small fst-italic" title="{{ $art->review_note }}">&ldquo;{{ Str::limit($art->review_note, 45) }}&rdquo;</span>
                            @else
                            <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No approval records match the criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
        <div class="card-footer bg-transparent py-3 d-print-none">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
