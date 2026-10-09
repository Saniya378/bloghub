@extends('layouts.dashboard')

@section('title', 'Author Activity & Productivity Report')
@section('report_heading', 'Author Activity & Productivity')
@section('report_subheading', 'Evaluate individual author output, review queue velocity, and reader readership metrics.')

@section('report_actions')
<a href="{{ route('admin.reports.authors.export.activity', request()->query()) }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Productivity CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Productivity KPI row (PDF Section 3.B) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-primary">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Authors</div>
                    <div class="h3 fw-bold my-1 text-primary" style="font-family: var(--font-meta);">{{ number_format($totals['total_authors']) }}</div>
                    <div class="small text-muted">Active Writers</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-info">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Submitted</div>
                    <div class="h3 fw-bold my-1 text-info" style="font-family: var(--font-meta);">{{ number_format($totals['total_submitted']) }}</div>
                    <div class="small text-muted">All Time</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-success">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Published</div>
                    <div class="h3 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($totals['total_published']) }}</div>
                    <div class="small text-muted">Approved Posts</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-warning">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Pending</div>
                    <div class="h3 fw-bold my-1 text-warning" style="font-family: var(--font-meta);">{{ number_format($totals['total_pending']) }}</div>
                    <div class="small text-muted">In Queue</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-danger">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Rejected</div>
                    <div class="h3 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($totals['total_rejected']) }}</div>
                    <div class="small text-muted">Sent Back</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-secondary">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Views</div>
                    <div class="h3 fw-bold my-1" style="color: var(--color-accent); font-family: var(--font-meta);">{{ number_format($totals['total_views']) }}</div>
                    <div class="small text-muted">Impressions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Sort Bar -->
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.authors.activity') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold mb-1">Search Author</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-4">
                    <label class="form-label small fw-semibold mb-1">Sort Productivity By</label>
                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="published" {{ request('sort', 'published') === 'published' ? 'selected' : '' }}>Published Articles Count</option>
                        <option value="views" {{ request('sort') === 'views' ? 'selected' : '' }}>Total Article Views</option>
                        <option value="submitted" {{ request('sort') === 'submitted' ? 'selected' : '' }}>Total Submitted Articles</option>
                        <option value="pending" {{ request('sort') === 'pending' ? 'selected' : '' }}>Pending Review Articles</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Apply
                    </button>
                    @if(request()->hasAny(['search', 'sort']))
                    <a href="{{ route('admin.reports.authors.activity') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Report Table (PDF Section 3.B) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-ranking-star text-warning me-2"></i>Author Productivity League Table
            </h5>
            <span class="badge bg-light text-dark border">{{ $authors->total() }} Authors</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th>Author</th>
                        <th class="text-center">Submitted</th>
                        <th class="text-center">Published</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Rejected</th>
                        <th class="text-end">Total Views</th>
                        <th class="text-end">Total Likes</th>
                        <th class="text-center">Approval Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($authors as $idx => $author)
                    @php
                        $rate = $author->submitted_count > 0 ? round(($author->published_count / $author->submitted_count) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="text-center fw-bold text-muted" style="font-family: var(--font-meta);">
                            {{ $authors->firstItem() + $idx }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $author->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($author->name) }}" 
                                     class="rounded-circle" width="34" height="34" alt="{{ $author->name }}" style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $author->name }}</div>
                                    <small class="text-muted">{{ $author->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center fw-bold" style="font-family: var(--font-meta);">
                            {{ $author->submitted_count }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1" style="font-family: var(--font-meta);">
                                {{ $author->published_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $author->pending_count > 0 ? 'bg-warning-subtle text-warning' : 'bg-light text-muted border' }} px-2 py-1" style="font-family: var(--font-meta);">
                                {{ $author->pending_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $author->rejected_count > 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-muted border' }} px-2 py-1" style="font-family: var(--font-meta);">
                                {{ $author->rejected_count }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-primary" style="font-family: var(--font-meta);">
                            {{ number_format((int) ($author->total_views ?? 0)) }}
                        </td>
                        <td class="text-end text-danger" style="font-family: var(--font-meta);">
                            <i class="fa-solid fa-heart me-1 small"></i>{{ number_format((int) ($author->total_likes ?? 0)) }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="progress" style="width: 60px; height: 6px;">
                                    <div class="progress-bar {{ $rate >= 70 ? 'bg-success' : ($rate >= 40 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $rate }}%"></div>
                                </div>
                                <small class="fw-bold" style="font-family: var(--font-meta); font-size: 0.75rem;">{{ $rate }}%</small>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No author activity records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($authors->hasPages())
        <div class="card-footer bg-transparent py-3 d-print-none">
            {{ $authors->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
