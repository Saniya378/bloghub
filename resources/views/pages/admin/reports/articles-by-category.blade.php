@extends('layouts.dashboard')

@section('title', 'Articles by Category Report')
@section('report_heading', 'Articles Grouped by Category')
@section('report_subheading', 'Topic distribution, readership interest, and category content volume breakdown.')

@section('report_actions')
<a href="{{ route('admin.reports.articles.export') }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Category CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Horizontal Category Bar Chart Visualization (PDF Section 4.D) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-chart-simple text-primary me-2"></i>Category Volume Distribution
            </h5>
            <span class="badge bg-light text-dark border">{{ count($categoryData) }} Categories</span>
        </div>
        <div class="card-body">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-7">
                    <div style="position: relative; height: 260px;">
                        <canvas id="categoryHorizontalBarChart"></canvas>
                    </div>
                </div>
                <div class="col-12 col-lg-5 border-start-lg">
                    <h6 class="small fw-bold text-uppercase text-muted mb-3" style="font-family: var(--font-meta);">Top Topics by Share</h6>
                    @foreach(array_slice($categoryData, 0, 4) as $cat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold text-dark"><i class="{{ $cat['icon'] ?? 'fa-solid fa-folder' }} text-muted me-1"></i> {{ $cat['name'] }}</span>
                            <span class="fw-bold" style="font-family: var(--font-meta);">{{ $cat['total_articles'] }} posts ({{ $cat['share'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $cat['share'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Table of Category Breakdown (PDF Section 4.D) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-folder-open text-warning me-2"></i>Category Inventory & Readership
            </h5>
            <span class="text-muted small">Total Articles: <strong>{{ number_format($totalArticlesAll) }}</strong></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Category</th>
                        <th class="text-center">Total Articles</th>
                        <th class="text-center">Published</th>
                        <th class="text-center">Pending</th>
                        <th class="text-end">Total Views</th>
                        <th class="text-end">Total Likes</th>
                        <th class="text-end">Platform Share</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryData as $cat)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary-subtle text-primary" style="width: 32px; height: 32px;">
                                    <i class="{{ $cat['icon'] ?? 'fa-solid fa-folder' }}"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $cat['name'] }}</div>
                                    <small class="text-muted" style="font-family: var(--font-meta);">slug: {{ $cat['slug'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center fw-bold" style="font-family: var(--font-meta);">
                            {{ $cat['total_articles'] }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1" style="font-family: var(--font-meta);">
                                {{ $cat['published'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $cat['pending'] > 0 ? 'bg-warning-subtle text-warning' : 'bg-light text-muted border' }} px-2 py-1" style="font-family: var(--font-meta);">
                                {{ $cat['pending'] }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-primary" style="font-family: var(--font-meta);">
                            {{ number_format($cat['views']) }}
                        </td>
                        <td class="text-end text-danger" style="font-family: var(--font-meta);">
                            <i class="fa-solid fa-heart me-1 small"></i>{{ number_format($cat['likes']) }}
                        </td>
                        <td class="text-end fw-bold" style="font-family: var(--font-meta);">
                            {{ $cat['share'] }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No categories configured.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('categoryHorizontalBarChart');
    if (ctx) {
        const catData = @json($categoryData);
        const labels = catData.map(c => c.name);
        const counts = catData.map(c => c.total_articles);
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
                        label: 'Articles',
                        data: counts,
                        backgroundColor: isDark ? '#DF572F' : '#1F2A44',
                        hoverBackgroundColor: '#C8461F',
                        borderRadius: 4,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1F2A44',
                            padding: 8,
                            cornerRadius: 6,
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { color: textColor, font: { family: 'IBM Plex Mono', size: 11 } }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: textColor, font: { family: 'Inter', size: 11 } }
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
@endsection
