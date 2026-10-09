@extends('layouts.dashboard')

@section('title', 'Engagement & Views Report')
@section('report_heading', 'Engagement & Audience Reach')
@section('report_subheading', 'Deep dive into reader impressions, article view traffic, like counts, and reader discussions.')

@section('report_actions')
<a href="{{ route('admin.reports.engagement.export', request()->query()) }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Engagement CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Engagement KPI Totals (PDF Section 6) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-success">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Views</div>
                    <div class="h3 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($totals['total_views']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-eye me-1"></i>Readership</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-danger">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Likes</div>
                    <div class="h3 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($totals['total_likes']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-heart me-1"></i>Reactions</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-primary">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Comments</div>
                    <div class="h3 fw-bold my-1 text-primary" style="font-family: var(--font-meta);">{{ number_format($totals['total_comments']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-comments me-1"></i>Discussions</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-info">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Bookmarks</div>
                    <div class="h3 fw-bold my-1 text-info" style="font-family: var(--font-meta);">{{ number_format($totals['total_bookmarks']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-bookmark me-1"></i>Saved Articles</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-warning">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Author Follows</div>
                    <div class="h3 fw-bold my-1 text-warning" style="font-family: var(--font-meta);">{{ number_format($totals['total_follows']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-user-plus me-1"></i>Subscribed</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-0 border-top border-4 border-secondary">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Avg Likes / Post</div>
                    @php
                        $publishedCount = \App\Models\Article::where('status', 'published')->count();
                        $avgLikes = $publishedCount > 0 ? round($totals['total_likes'] / $publishedCount, 1) : 0;
                    @endphp
                    <div class="h3 fw-bold my-1 text-dark" style="font-family: var(--font-meta);">{{ $avgLikes }}</div>
                    <div class="small text-muted">Per Article</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Range Selector & Filter Bar (PDF Section 6.A) -->
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.engagement.views') }}" class="row g-3 align-items-end">
                <div class="col-12 col-lg-auto">
                    <label class="form-label small fw-semibold mb-1">Time Range Preset</label>
                    <div class="btn-group btn-group-sm d-flex flex-wrap" role="group">
                        <a href="{{ route('admin.reports.engagement.views', ['range' => 'all']) }}" class="btn {{ $timeRange === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">All Time</a>
                        <a href="{{ route('admin.reports.engagement.views', ['range' => 'today']) }}" class="btn {{ $timeRange === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Today</a>
                        <a href="{{ route('admin.reports.engagement.views', ['range' => 'week']) }}" class="btn {{ $timeRange === 'week' ? 'btn-primary' : 'btn-outline-secondary' }}">This Week</a>
                        <a href="{{ route('admin.reports.engagement.views', ['range' => 'month']) }}" class="btn {{ $timeRange === 'month' ? 'btn-primary' : 'btn-outline-secondary' }}">This Month</a>
                        <a href="{{ route('admin.reports.engagement.views', ['range' => 'year']) }}" class="btn {{ $timeRange === 'year' ? 'btn-primary' : 'btn-outline-secondary' }}">This Year</a>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Search Article Title</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Sort Metric</label>
                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="views" {{ request('sort', 'views') === 'views' ? 'selected' : '' }}>Highest Views</option>
                        <option value="likes" {{ request('sort') === 'likes' ? 'selected' : '' }}>Highest Likes</option>
                        <option value="comments" {{ request('sort') === 'comments' ? 'selected' : '' }}>Most Comments</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 d-flex gap-2">
                    <input type="hidden" name="range" value="{{ $timeRange }}">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Apply
                    </button>
                    @if(request()->hasAny(['search', 'sort', 'range']))
                    <a href="{{ route('admin.reports.engagement.views') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Engagement Table (PDF Section 6.B) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-fire text-danger me-2"></i>Most Popular & Engaged Articles
            </h5>
            <span class="badge bg-light text-dark border">{{ $articles->total() }} Articles</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th>Article Title</th>
                        <th>Author</th>
                        <th class="text-center">Published</th>
                        <th class="text-end">Views</th>
                        <th class="text-end">Likes</th>
                        <th class="text-center">Comments</th>
                        <th class="text-center">Engagement Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $idx => $art)
                    @php
                        $engagementScore = $art->views > 0 ? round((($art->likes + $art->comments_count) / $art->views) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="text-center fw-bold text-muted" style="font-family: var(--font-meta);">
                            {{ $articles->firstItem() + $idx }}
                        </td>
                        <td>
                            <a href="{{ url('/blogs/' . $art->slug) }}" target="_blank" class="fw-semibold text-dark text-decoration-none text-truncate d-block" style="max-width: 280px;">
                                {{ $art->title }}
                            </a>
                            <small class="badge bg-light text-muted border py-0 px-1">{{ $art->category_slug ?? 'General' }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $art->user?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($art->user?->name ?? 'User') }}" 
                                     class="rounded-circle" width="26" height="26" alt="{{ $art->user?->name }}" style="object-fit: cover;">
                                <span class="small fw-medium text-dark">{{ $art->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="text-center" style="font-family: var(--font-meta); font-size: 0.8125rem;">
                            {{ $art->published_at ? $art->published_at->format('d M Y') : 'Draft' }}
                        </td>
                        <td class="text-end fw-bold text-primary" style="font-family: var(--font-meta);">
                            {{ number_format($art->views) }}
                        </td>
                        <td class="text-end text-danger" style="font-family: var(--font-meta);">
                            <i class="fa-solid fa-heart me-1 small"></i>{{ number_format($art->likes) }}
                        </td>
                        <td class="text-center text-muted" style="font-family: var(--font-meta);">
                            <i class="fa-solid fa-comment me-1 small"></i>{{ number_format($art->comments_count) }}
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $engagementScore > 10 ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} px-2 py-1" style="font-family: var(--font-meta);">
                                {{ $engagementScore }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No articles found for the selected time range.</td>
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
