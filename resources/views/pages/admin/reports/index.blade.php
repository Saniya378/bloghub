@extends('layouts.dashboard')

@section('title', 'Reports Dashboard')
@section('report_heading', 'Reports Dashboard')
@section('report_subheading', 'Core publishing metrics, author throughput, content distribution, and activity trends.')

@section('report_actions')
<a href="{{ route('admin.reports.export') }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Overview CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Summary Cards (Section 2 from PDF) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-0 report-stat-card border-top border-4 border-primary">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Authors</div>
                    <div class="display-6 fw-bold my-1 text-primary" style="font-family: var(--font-meta);">{{ number_format($totalAuthors) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-user-pen me-1"></i>Registered Creators</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-0 report-stat-card border-top border-4 border-info">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Articles</div>
                    <div class="display-6 fw-bold my-1 text-dark" style="font-family: var(--font-meta);">{{ number_format($totalArticles) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-newspaper me-1"></i>All Platform Posts</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-0 report-stat-card border-top border-4 border-success">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Views</div>
                    <div class="display-6 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($totalViews) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-eye me-1"></i>Reader Impressions</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-0 report-stat-card border-top border-4 border-warning">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Pending Authors</div>
                    <div class="display-6 fw-bold my-1 text-warning" style="font-family: var(--font-meta);">{{ number_format($pendingAuthors) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-user-clock me-1"></i>Awaiting Upgrades</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-0 report-stat-card border-top border-4 border-danger">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Pending Articles</div>
                    <div class="display-6 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($pendingArticles) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-hourglass-half me-1"></i>Review Queue</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-0 report-stat-card border-top border-4 border-secondary">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Published</div>
                    <div class="display-6 fw-bold my-1" style="color: var(--color-accent); font-family: var(--font-meta);">{{ number_format($publishedArticles) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-circle-check me-1"></i>Live Articles</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row (Section 2 from PDF) -->
    <div class="row g-4 mb-4">
        <!-- Monthly Activity Chart -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                            <i class="fa-solid fa-chart-column text-primary me-2"></i>Monthly Activity
                        </h5>
                        <small class="text-muted">Articles published per month over the last 12 months</small>
                    </div>
                    <span class="badge bg-light text-dark border"><i class="fa-regular fa-calendar me-1"></i> Last 12 Months</span>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="monthlyActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Donuts (Articles & Authors) -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                        <i class="fa-solid fa-chart-pie text-accent me-2" style="color: var(--color-accent);"></i>Distribution Breakdown
                    </h5>
                    <small class="text-muted">Articles and authors status ratios</small>
                </div>
                <div class="card-body">
                    <h6 class="small fw-bold text-uppercase text-muted mb-2" style="font-family: var(--font-meta);">Articles Status</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> Published</span>
                            <span class="fw-bold">{{ $articleBreakdown['published'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalArticles > 0 ? ($articleBreakdown['published'] / $totalArticles) * 100 : 0 }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fa-solid fa-circle text-warning me-1" style="font-size: 8px;"></i> Pending Review</span>
                            <span class="fw-bold">{{ $articleBreakdown['pending'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalArticles > 0 ? ($articleBreakdown['pending'] / $totalArticles) * 100 : 0 }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fa-solid fa-circle text-danger me-1" style="font-size: 8px;"></i> Rejected</span>
                            <span class="fw-bold">{{ $articleBreakdown['rejected'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalArticles > 0 ? ($articleBreakdown['rejected'] / $totalArticles) * 100 : 0 }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fa-solid fa-circle text-secondary me-1" style="font-size: 8px;"></i> Drafts</span>
                            <span class="fw-bold">{{ $articleBreakdown['draft'] }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $totalArticles > 0 ? ($articleBreakdown['draft'] / $totalArticles) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <h6 class="small fw-bold text-uppercase text-muted mt-4 mb-2" style="font-family: var(--font-meta);">Authors Status</h6>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded mb-2 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-1"><i class="fa-solid fa-check"></i></span>
                            <span class="small fw-semibold">Approved / Active</span>
                        </div>
                        <span class="badge bg-white text-dark border fw-bold">{{ $authorBreakdown['approved'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded mb-2 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning rounded-circle p-1 text-dark"><i class="fa-solid fa-clock"></i></span>
                            <span class="small fw-semibold">Pending / Readers</span>
                        </div>
                        <span class="badge bg-white text-dark border fw-bold">{{ $authorBreakdown['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger rounded-circle p-1"><i class="fa-solid fa-ban"></i></span>
                            <span class="small fw-semibold">Blocked / Inactive</span>
                        </div>
                        <span class="badge bg-white text-dark border fw-bold">{{ $authorBreakdown['blocked'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row: Top Authors & Top 10 Articles -->
    <div class="row g-4 mb-4">
        <!-- Top Authors -->
        <div class="col-12 col-xl-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                        <i class="fa-solid fa-trophy text-warning me-2"></i>Top Productive Authors
                    </h5>
                    <a href="{{ route('admin.reports.authors.activity') }}" class="btn btn-sm btn-link text-decoration-none">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Author</th>
                                <th class="text-center">Published</th>
                                <th class="text-end">Total Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topAuthors as $idx => $author)
                            <tr>
                                <td class="text-center">
                                    <span class="badge {{ $idx === 0 ? 'bg-warning text-dark' : ($idx === 1 ? 'bg-secondary' : ($idx === 2 ? 'bg-bronze text-white' : 'bg-light text-dark border')) }} rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ $idx + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $author->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($author->name) }}" 
                                             class="rounded-circle" width="32" height="32" alt="{{ $author->name }}" style="object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $author->name }}</div>
                                            <small class="text-muted">{{ $author->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">{{ $author->articles_count }}</span>
                                </td>
                                <td class="text-end fw-bold" style="font-family: var(--font-meta);">
                                    {{ number_format((int) ($author->total_views ?? 0)) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No author data available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top 10 Most Viewed Articles -->
        <div class="col-12 col-xl-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                        <i class="fa-solid fa-fire text-danger me-2"></i>Top 10 Most Viewed Articles
                    </h5>
                    <a href="{{ route('admin.reports.engagement.views') }}" class="btn btn-sm btn-link text-decoration-none">Full Engagement <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">#</th>
                                <th>Article Title</th>
                                <th>Author</th>
                                <th class="text-center">Views</th>
                                <th class="text-center">Likes</th>
                                <th class="text-center">Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topArticles as $idx => $art)
                            <tr>
                                <td class="text-center fw-bold text-muted" style="font-family: var(--font-meta);">{{ $idx + 1 }}</td>
                                <td>
                                    <a href="{{ url('/blogs/' . $art->slug) }}" target="_blank" class="fw-semibold text-dark text-decoration-none hover-primary text-truncate d-block" style="max-width: 260px;">
                                        {{ $art->title }}
                                    </a>
                                    <small class="badge bg-light text-muted border py-0 px-1">{{ $art->category_slug ?? 'General' }}</small>
                                </td>
                                <td>
                                    <small class="fw-medium text-dark">{{ $art->user?->name ?? 'Unknown' }}</small>
                                </td>
                                <td class="text-center fw-bold text-primary" style="font-family: var(--font-meta);">
                                    {{ number_format($art->views) }}
                                </td>
                                <td class="text-center text-danger" style="font-family: var(--font-meta);">
                                    <i class="fa-solid fa-heart me-1 small"></i>{{ number_format($art->likes) }}
                                </td>
                                <td class="text-center text-muted" style="font-family: var(--font-meta);">
                                    <i class="fa-solid fa-comment me-1 small"></i>{{ number_format($art->comment_count ?? $art->comments()->count()) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No published articles yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('monthlyActivityChart');
    if (ctx) {
        const labels = @json($monthLabels);
        const data = @json($monthlyArticles);
        let chartInstance = null;

        function renderChart() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const gridColor = isDark ? '#2B374D' : '#E8E5DD';
            const textColor = isDark ? '#9DA7B8' : '#575E6C';

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Articles Published',
                        data: data,
                        backgroundColor: isDark ? '#DF572F' : 'rgba(31, 42, 68, 0.85)',
                        hoverBackgroundColor: '#C8461F',
                        borderColor: isDark ? '#DF572F' : '#1F2A44',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1F2A44',
                            titleFont: { family: 'Inter' },
                            bodyFont: { family: 'Inter' },
                            padding: 10,
                            cornerRadius: 6,
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor, font: { family: 'IBM Plex Mono', size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor, stepSize: 1, font: { family: 'IBM Plex Mono', size: 11 } }
                        }
                    }
                }
            });
        }

        renderChart();

        new MutationObserver(() => renderChart())
            .observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    }
});
</script>
@endpush

@push('styles')
<style>
    .bg-bronze { background-color: #cd7f32; }
    .report-stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .report-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.08) !important;
    }
    .hover-primary:hover {
        color: var(--color-accent) !important;
    }
</style>
@endpush
@endsection
