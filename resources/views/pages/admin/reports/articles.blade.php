@extends('layouts.dashboard')

@section('title', 'Article Summary & Status Report')
@section('report_heading', 'Article Summary & Status Report')
@section('report_subheading', 'Comprehensive inventory of blog articles, review statuses, categorization, and engagement performance.')

@section('report_actions')
<a href="{{ route('admin.reports.articles.export', request()->query()) }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Articles CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Article Status Report Graphical Cards (PDF Section 4.B) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-top border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Published</span>
                        <span class="badge bg-success-subtle text-success fw-bold">{{ $statusStats['published']['share'] }}%</span>
                    </div>
                    <div class="h3 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($statusStats['published']['count']) }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ $statusStats['published']['share'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-top border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Pending Review</span>
                        <span class="badge bg-warning-subtle text-warning fw-bold">{{ $statusStats['pending']['share'] }}%</span>
                    </div>
                    <div class="h3 fw-bold my-1 text-warning" style="font-family: var(--font-meta);">{{ number_format($statusStats['pending']['count']) }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: {{ $statusStats['pending']['share'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-top border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Rejected</span>
                        <span class="badge bg-danger-subtle text-danger fw-bold">{{ $statusStats['rejected']['share'] }}%</span>
                    </div>
                    <div class="h3 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($statusStats['rejected']['count']) }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: {{ $statusStats['rejected']['share'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-top border-4 border-secondary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Drafts</span>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">{{ $statusStats['draft']['share'] }}%</span>
                    </div>
                    <div class="h3 fw-bold my-1 text-secondary" style="font-family: var(--font-meta);">{{ number_format($statusStats['draft']['count']) }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-secondary" style="width: {{ $statusStats['draft']['share'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar (PDF Section 4.A & Section 8) -->
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.articles') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Search Article</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search title or text..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Author</label>
                    <select name="author_id" class="form-select form-select-sm">
                        <option value="">All Authors</option>
                        @foreach($authors as $auth)
                        <option value="{{ $auth->id }}" {{ request('author_id') == $auth->id ? 'selected' : '' }}>{{ $auth->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Category</label>
                    <select name="category_slug" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category_slug') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'author_id', 'category_slug', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.reports.articles') }}" class="btn btn-sm btn-outline-secondary">
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

    <!-- Articles Table (PDF Section 4.A) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-newspaper text-primary me-2"></i>Articles Log & Performance
            </h5>
            <span class="badge bg-light text-dark border">{{ $articles->total() }} Articles Listed</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Article</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Submitted / Date</th>
                        <th>Status</th>
                        <th class="text-end">Views</th>
                        <th class="text-end">Likes</th>
                        <th class="text-center">Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $art)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $art->title }}</div>
                            <small class="text-muted text-truncate d-block" style="max-width: 280px;">{{ $art->excerpt ?? Str::limit(strip_tags($art->content), 60) }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $art->user?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($art->user?->name ?? 'User') }}" 
                                     class="rounded-circle" width="28" height="28" alt="{{ $art->user?->name }}" style="object-fit: cover;">
                                <span class="fw-medium text-dark small">{{ $art->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ ucfirst($art->category_slug ?? 'General') }}</span>
                        </td>
                        <td style="font-family: var(--font-meta); font-size: 0.8125rem;">
                            {{ $art->submitted_at ? $art->submitted_at->format('d M Y') : $art->created_at->format('d M Y') }}
                        </td>
                        <td>
                            @if($art->status === 'published')
                                <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>Published</span>
                            @elseif($art->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning px-2 py-1"><i class="fa-solid fa-hourglass-half me-1"></i>Pending</span>
                            @elseif($art->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i>Rejected</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="fa-solid fa-pencil me-1"></i>Draft</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-primary" style="font-family: var(--font-meta);">
                            {{ number_format($art->views) }}
                        </td>
                        <td class="text-end text-danger" style="font-family: var(--font-meta);">
                            <i class="fa-solid fa-heart me-1 small"></i>{{ number_format($art->likes) }}
                        </td>
                        <td class="text-center text-muted" style="font-family: var(--font-meta);">
                            {{ number_format($art->comments_count) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No articles match the selected filters.</td>
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
