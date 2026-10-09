@extends('layouts.dashboard')

@section('title', 'Overview — Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Dashboard Overview</h1>
        <p class="text-muted mb-0">Here is how your posts are doing.</p>
    </div>
    <a href="{{ url('/dashboard/posts/create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>New Post</a>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-4 col-lg-2">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-primary">{{ $stats['published'] }}</div>
                <small class="text-muted">Published</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-warning">{{ $stats['drafts'] }}</div>
                <small class="text-muted">Drafts</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-secondary">{{ $stats['pending'] }}</div>
                <small class="text-muted">In Review</small>
            </div>
        </div>
    </div>
    @if($stats['rejected'] > 0)
    <div class="col-md-4 col-lg-2">
        <div class="card border-danger">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-danger">{{ $stats['rejected'] }}</div>
                <small class="text-muted">Rejected</small>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-4 col-lg-2">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-info">{{ number_format($stats['total_views']) }}</div>
                <small class="text-muted">Views</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-danger">{{ number_format($stats['total_likes']) }}</div>
                <small class="text-muted">Likes</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-success">{{ number_format($stats['total_comments']) }}</div>
                <small class="text-muted">Comments</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-secondary">{{ $stats['followers'] }}</div>
                <small class="text-muted">Followers</small>
            </div>
        </div>
    </div>
</div>

<!-- Views Chart -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Views per Post (Top 10)</h5>
    </div>
    <div class="card-body">
        <div style="position: relative; height: 350px;">
            <canvas id="viewsChart" style="width: 100%; height: 100%;"></canvas>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('viewsChart');
    const ctx = canvas.getContext('2d');
    const data = @json($postsForChart->map(fn($p) => ['title' => Str::limit($p['title'], 20), 'views' => $p['views']]));

    // Read theme-aware colors from CSS variables
    const css = getComputedStyle(document.documentElement);
    const colorPrimary = css.getPropertyValue('--color-primary').trim() || '#1F2A44';
    const colorTextMuted = css.getPropertyValue('--color-text-muted').trim() || '#575E6C';
    const colorBorder = css.getPropertyValue('--color-border').trim() || '#D8D5CC';
    const colorBorderSubtle = css.getPropertyValue('--color-border-subtle').trim() || '#E8E5DD';
    const colorTextLight = css.getPropertyValue('--color-text-light').trim() || '#838B9B';

    function draw() {
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = 350 * dpr;
        ctx.scale(dpr, dpr);

        const width = rect.width;
        const height = 350;
        const padding = { top: 20, right: 50, bottom: 40, left: 170 };
        const chartW = width - padding.left - padding.right;
        const chartH = height - padding.top - padding.bottom;

        if (data.length === 0) {
            ctx.fillStyle = colorTextMuted;
            ctx.font = '14px sans-serif';
            ctx.fillText('No data yet — publish some posts!', 20, 50);
            return;
        }

        const maxViews = Math.max(...data.map(d => d.views));
        const barHeight = chartH / data.length * 0.7;
        const gap = chartH / data.length * 0.3;

        // Draw horizontal bars (labels on the left, fully readable)
        data.forEach((d, i) => {
            const y = padding.top + i * (barHeight + gap) + gap / 2;
            const barW = (d.views / maxViews) * chartW;
            const x = padding.left;

            ctx.fillStyle = colorPrimary;
            ctx.fillRect(x, y, barW, barHeight);

            // Value label (after the bar)
            ctx.fillStyle = colorTextMuted;
            ctx.font = '11px sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText(d.views >= 1000 ? (d.views / 1000).toFixed(1) + 'k' : d.views, x + barW + 6, y + barHeight / 2 + 4);

            // Title label (left side, no rotation)
            ctx.textAlign = 'right';
            ctx.fillText(d.title, padding.left - 10, y + barHeight / 2 + 4);
        });

        // Axes
        ctx.strokeStyle = colorBorder;
        ctx.beginPath();
        ctx.moveTo(padding.left, padding.top);
        ctx.lineTo(padding.left, padding.top + chartH);
        ctx.lineTo(padding.left + chartW, padding.top + chartH);
        ctx.stroke();

        // Grid lines (vertical)
        for (let i = 0; i <= 4; i++) {
            const x = padding.left + (chartW / 4) * i;
            ctx.strokeStyle = colorBorderSubtle;
            ctx.beginPath();
            ctx.moveTo(x, padding.top);
            ctx.lineTo(x, padding.top + chartH);
            ctx.stroke();

            ctx.fillStyle = colorTextLight;
            ctx.font = '10px sans-serif';
            ctx.textAlign = 'center';
            const val = Math.round((maxViews / 4) * i);
            ctx.fillText(val >= 1000 ? (val / 1000).toFixed(1) + 'k' : val, x, height - padding.bottom + 18);
        }
    }

    draw();

    // Redraw on resize (debounced)
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(draw, 150);
    });
});
</script>
@endpush
