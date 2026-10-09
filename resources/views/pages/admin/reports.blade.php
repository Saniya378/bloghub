@extends('layouts.dashboard')

@section('title', 'Reports')

@section('content')

@php
    $rangeQuery = ['range' => $range['preset']];
    if ($range['preset'] === 'custom') {
        $rangeQuery['from'] = $range['from']->toDateString();
        $rangeQuery['to'] = $range['to']->toDateString();
    }
@endphp

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Growth Reports</h1>
        <p class="text-muted mb-0">{{ $range['label'] }} &middot; compared with the previous {{ $range['days'] }} days.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.export', $rangeQuery) }}" class="btn btn-bh btn-bh-primary">
            <i class="fa-solid fa-file-csv me-2"></i>Export CSV
        </a>
    </div>
</div>

<!-- Date range filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end">
            <div class="col-12 col-lg-auto">
                <label class="form-label mb-1 small fw-semibold">Quick range</label>
                <div class="btn-group" role="group" aria-label="Quick date range">
                    @foreach(['7' => '7 days', '30' => '30 days', '90' => '90 days'] as $value => $text)
                    {{-- PHP casts numeric array keys to int, so compare as strings --}}
                    <a href="{{ route('admin.reports', ['range' => $value]) }}"
                       class="btn btn-sm {{ $range['preset'] === (string) $value ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $text }}</a>
                    @endforeach
                </div>
            </div>

            <div class="col-6 col-lg-auto">
                <label for="filterFrom" class="form-label mb-1 small fw-semibold">From</label>
                <input type="date" id="filterFrom" name="from" class="form-control form-control-sm"
                       value="{{ $range['from']->toDateString() }}">
            </div>

            <div class="col-6 col-lg-auto">
                <label for="filterTo" class="form-label mb-1 small fw-semibold">To</label>
                <input type="date" id="filterTo" name="to" class="form-control form-control-sm"
                       value="{{ $range['to']->toDateString() }}">
            </div>

            <div class="col-12 col-lg-auto d-flex gap-2 align-items-center">
                <input type="hidden" name="range" value="custom">
                <button type="submit" class="btn btn-sm btn-bh btn-bh-primary">
                    <i class="fa-solid fa-filter me-1"></i>Apply
                </button>

                @if($range['preset'] === 'custom')
                <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-link text-decoration-none">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Metric switcher + chart -->
<div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="mb-0">Activity Over Time</h5>
        <div class="btn-group flex-wrap" role="group" aria-label="Choose metric">
            @foreach(\App\Http\Controllers\ReportsController::METRICS as $key => $meta)
            <button type="button"
                    class="btn btn-sm report-metric {{ $loop->first ? 'btn-primary' : 'btn-outline-secondary' }}"
                    data-metric="{{ $key }}">{{ $meta['label'] }}</button>
            @endforeach
        </div>
    </div>
    <div class="card-body">
        <div style="position: relative; height: 340px;">
            <canvas id="growthChart" style="width: 100%; height: 100%;"></canvas>
        </div>
        <p class="text-muted small mb-0 mt-3">
            <i class="fa-solid fa-circle-info me-1"></i>
            Page views are tracked as a running total on each post, so they are reported as a
            current figure below rather than as a per-day series. Every other metric is counted
            from its own event timestamp.
        </p>
    </div>
</div>

<!-- KPI cards -->
<div class="row g-3 mb-4">
    @foreach(\App\Http\Controllers\ReportsController::METRICS as $key => $meta)
    @php
        $delta = $deltas[$key];
        $total = $totals[$key];
        $previous = $previousTotals[$key];
        $avg = $range['days'] > 0 ? round($total / $range['days'], 1) : 0;
    @endphp
    <div class="col-6 col-lg-4 col-xl-2">
        <div class="card h-100 report-kpi" data-metric="{{ $key }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <small class="text-muted text-uppercase report-kpi__label">{{ $meta['label'] }}</small>
                    <i class="{{ $meta['icon'] }} report-kpi__icon" style="color: {{ $meta['color'] }};"></i>
                </div>
                <div class="h3 mb-1 mt-2">{{ number_format($total) }}</div>
                <div class="small">
                    @if($delta === null)
                        <span class="text-success"><i class="fa-solid fa-arrow-trend-up me-1"></i>New</span>
                        <span class="text-muted">no prior activity</span>
                    @elseif($delta > 0)
                        <span class="text-success"><i class="fa-solid fa-arrow-up me-1"></i>+{{ number_format($delta, 1) }}%</span>
                    @elseif($delta < 0)
                        <span class="text-danger"><i class="fa-solid fa-arrow-down me-1"></i>{{ number_format(abs($delta), 1) }}%</span>
                    @else
                        <span class="text-muted"><i class="fa-solid fa-minus me-1"></i>No change</span>
                    @endif
                </div>
                <small class="text-muted d-block mt-1">vs {{ number_format($previous) }} previous</small>
                <small class="text-muted d-block">{{ $avg }} / day avg</small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <!-- Current platform snapshot -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Platform Snapshot</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        <tr><td class="text-muted">Total views</td><td class="text-end fw-bold">{{ number_format($snapshot['views']) }}</td></tr>
                        <tr><td class="text-muted">Total post likes</td><td class="text-end fw-bold">{{ number_format($snapshot['likes']) }}</td></tr>
                        <tr><td class="text-muted">Users</td><td class="text-end fw-bold">{{ number_format($snapshot['users']) }}</td></tr>
                        <tr><td class="text-muted">Authors</td><td class="text-end fw-bold">{{ number_format($snapshot['authors']) }}</td></tr>
                        <tr><td class="text-muted">Readers</td><td class="text-end fw-bold">{{ number_format($snapshot['readers']) }}</td></tr>
                        <tr><td class="text-muted">Posts</td><td class="text-end fw-bold">{{ number_format($snapshot['posts']) }}</td></tr>
                        <tr><td class="text-muted">Published</td><td class="text-end fw-bold">{{ number_format($snapshot['published']) }}</td></tr>
                        <tr><td class="text-muted">Drafts</td><td class="text-end fw-bold">{{ number_format($snapshot['drafts']) }}</td></tr>
                        <tr><td class="text-muted">Posts awaiting review</td><td class="text-end fw-bold">{{ number_format($snapshot['pending']) }}</td></tr>
                        <tr><td class="text-muted">Comments</td><td class="text-end fw-bold">{{ number_format($snapshot['comments']) }}</td></tr>
                        <tr><td class="text-muted">Comments awaiting moderation</td><td class="text-end fw-bold">{{ number_format($snapshot['pending_comments']) }}</td></tr>
                        <tr><td class="text-muted">Newsletter subscribers</td><td class="text-end fw-bold">{{ number_format($snapshot['subscribers']) }}</td></tr>
                        <tr><td class="text-muted">Unread messages</td><td class="text-end fw-bold">{{ number_format($snapshot['unread_messages']) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- In-range extras -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">In This Period</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        <tr><td class="text-muted">Posts published</td><td class="text-end fw-bold">{{ number_format($growth['published']) }}</td></tr>
                        <tr><td class="text-muted">Comments written</td><td class="text-end fw-bold">{{ number_format($totals['comments']) }}</td></tr>
                        <tr><td class="text-muted">Comments awaiting moderation</td><td class="text-end fw-bold">{{ number_format($growth['pending_comments']) }}</td></tr>
                        <tr><td class="text-muted">Newsletter subscribers added</td><td class="text-end fw-bold">{{ number_format($growth['subscribers']) }}</td></tr>
                        <tr><td class="text-muted">Contact messages received</td><td class="text-end fw-bold">{{ number_format($growth['messages']) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Daily breakdown -->
    <div class="col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Daily Breakdown</h5>
            </div>
            <div class="card-body p-0" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="sticky-top report-table-head">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th class="text-end pe-3">{{ \App\Http\Controllers\ReportsController::METRICS['likes']['label'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $likedSeries = $series['likes']; @endphp
                        @foreach(array_reverse($dates) as $date)
                        <tr>
                            <td class="ps-3 text-muted">{{ \Carbon\Carbon::parse($date)->format('M j') }}</td>
                            <td class="text-end pe-3 fw-bold">{{ number_format($likedSeries[$date] ?? 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('growthChart');
    if (!canvas) return;

    const dates = @json($dates);
    const series = @json($series);
    const meta = @json(\App\Http\Controllers\ReportsController::METRICS);
    const previous = @json($previousTotals);

    let currentMetric = Object.keys(series)[0];

    function themeColors() {
        const css = getComputedStyle(document.documentElement);
        return {
            primary: css.getPropertyValue('--color-primary').trim() || '#1F2A44',
            text: css.getPropertyValue('--color-text').trim() || '#212529',
            muted: css.getPropertyValue('--color-text-muted').trim() || '#575E6C',
            light: css.getPropertyValue('--color-text-light').trim() || '#838B9B',
            border: css.getPropertyValue('--color-border').trim() || '#D8D5CC',
            subtle: css.getPropertyValue('--color-border-subtle').trim() || '#E8E5DD',
            surface: css.getPropertyValue('--color-surface').trim() || '#FFFFFF'
        };
    }

    function compact(n) {
        n = Number(n) || 0;
        if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
        if (n >= 1000) return (n / 1000).toFixed(1) + 'k';
        return String(n);
    }

    function draw() {
        const colors = themeColors();
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.parentElement.getBoundingClientRect();
        const width = rect.width;
        const height = 340;

        canvas.width = width * dpr;
        canvas.height = height * dpr;
        const ctx = canvas.getContext('2d');
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, width, height);

        const padding = { top: 24, right: 24, bottom: 44, left: 56 };
        const chartW = width - padding.left - padding.right;
        const chartH = height - padding.top - padding.bottom;
        const values = series[currentMetric];
        const color = (meta[currentMetric] && meta[currentMetric].color) || colors.primary;

        // Round the axis maximum up to something readable
        const rawMax = Math.max(1, ...Object.values(values));
        const magnitude = Math.pow(10, Math.floor(Math.log10(rawMax)));
        const max = Math.ceil(rawMax / magnitude) * magnitude;
        const steps = 4;

        // Grid + Y labels
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        for (let i = 0; i <= steps; i++) {
            const y = padding.top + (chartH / steps) * i;
            const value = Math.round((max / steps) * (steps - i));

            ctx.strokeStyle = colors.subtle;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(padding.left + chartW, y);
            ctx.stroke();

            ctx.fillStyle = colors.light;
            ctx.fillText(compact(value), padding.left - 10, y);
        }

        const n = dates.length;
        const stepX = n > 1 ? chartW / (n - 1) : 0;
        const xAt = (i) => padding.left + (n > 1 ? i * stepX : chartW / 2);
        const yAt = (v) => padding.top + chartH - (v / max) * chartH;

        // X labels (about six, evenly spaced)
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        const labelEvery = Math.max(1, Math.ceil(n / 6));
        for (let i = 0; i < n; i += labelEvery) {
            const d = new Date(dates[i] + 'T00:00:00');
            ctx.fillStyle = colors.light;
            ctx.fillText(d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' }), xAt(i), padding.top + chartH + 10);
        }

        if (n === 0 || Object.values(values).every((v) => v === 0)) {
            ctx.fillStyle = colors.muted;
            ctx.font = '14px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('No activity recorded in this period', width / 2, padding.top + chartH / 2);
            return;
        }

        // Area fill
        ctx.beginPath();
        ctx.moveTo(xAt(0), yAt(values[dates[0]]));
        for (let i = 1; i < n; i++) ctx.lineTo(xAt(i), yAt(values[dates[i]]));
        ctx.lineTo(xAt(n - 1), padding.top + chartH);
        ctx.lineTo(xAt(0), padding.top + chartH);
        ctx.closePath();
        ctx.fillStyle = color + '1A';
        ctx.fill();

        // Line
        ctx.beginPath();
        ctx.moveTo(xAt(0), yAt(values[dates[0]]));
        for (let i = 1; i < n; i++) ctx.lineTo(xAt(i), yAt(values[dates[i]]));
        ctx.strokeStyle = color;
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.stroke();

        // Points, with emphasis on the latest day
        for (let i = 0; i < n; i++) {
            const isLast = i === n - 1;
            ctx.beginPath();
            ctx.arc(xAt(i), yAt(values[dates[i]]), isLast ? 4.5 : 2, 0, Math.PI * 2);
            ctx.fillStyle = isLast ? color : colors.surface;
            ctx.fill();
            ctx.strokeStyle = color;
            ctx.lineWidth = isLast ? 0 : 1.5;
            ctx.stroke();
        }

        // Axes
        ctx.strokeStyle = colors.border;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padding.left, padding.top);
        ctx.lineTo(padding.left, padding.top + chartH);
        ctx.lineTo(padding.left + chartW, padding.top + chartH);
        ctx.stroke();

        // Header: current metric label + period total vs previous period
        const total = Object.values(values).reduce((a, b) => a + b, 0);
        const before = previous[currentMetric] || 0;
        const delta = before === 0 ? null : Math.round(((total - before) / before) * 1000) / 10;

        ctx.textAlign = 'left';
        ctx.textBaseline = 'alphabetic';
        ctx.font = '600 13px sans-serif';
        ctx.fillStyle = colors.text;
        ctx.fillText(meta[currentMetric].label, padding.left, 16);

        ctx.font = '11px sans-serif';
        ctx.fillStyle = colors.muted;
        ctx.textAlign = 'right';
        const change = delta === null
            ? 'no prior activity'
            : (delta > 0 ? '+' : '') + delta + '% vs previous period';
        ctx.fillText(total + ' total — ' + change, padding.left + chartW, 16);

        // Hover tooltip
        canvas.onmousemove = (e) => {
            const rect = canvas.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;

            let index = 0;
            if (n > 1) index = Math.round((mouseX - padding.left) / stepX);
            index = Math.max(0, Math.min(n - 1, index));

            const value = values[dates[index]];
            const label = new Date(dates[index] + 'T00:00:00')
                .toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });

            tooltip.textContent = label + ' — ' + value;
            tooltip.style.display = 'block';

            const px = xAt(index);
            const py = yAt(value);
            tooltip.style.left = Math.max(0, Math.min(rect.width - 150, px - 60)) + 'px';
            tooltip.style.top = Math.max(0, py - 34) + 'px';

            marker.style.display = 'block';
            marker.style.left = px + 'px';
            marker.style.top = py + 'px';
        };

        canvas.onmouseleave = () => {
            tooltip.style.display = 'none';
            marker.style.display = 'none';
        };
    }

    const tooltip = document.createElement('div');
    tooltip.style.cssText = 'position:absolute;display:none;pointer-events:none;background:var(--color-primary);color:#fff;padding:4px 8px;border-radius:4px;font-size:12px;white-space:nowrap;z-index:5;';
    canvas.parentElement.appendChild(tooltip);

    const marker = document.createElement('div');
    marker.style.cssText = 'position:absolute;display:none;pointer-events:none;width:10px;height:10px;margin:-5px 0 0 -5px;border-radius:50%;background:var(--color-accent);box-shadow:0 0 0 3px rgba(200,70,31,.25);z-index:5;';
    canvas.parentElement.appendChild(marker);

    document.querySelectorAll('.report-metric').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.report-metric').forEach((b) => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.add('btn-primary');
            btn.classList.remove('btn-outline-secondary');
            currentMetric = btn.dataset.metric;
            draw();
        });
    });

    draw();

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(draw, 150);
    });

    // The chart bakes CSS variable colours into the canvas, so it has to be
    // repainted when the dark mode toggle flips data-theme on <html>.
    new MutationObserver(() => draw())
        .observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

    document.querySelector('[data-toggle="theme"]')?.addEventListener('click', () => {
        // data-theme is set synchronously before the observer fires; draw again
        // on the next frame so the new custom properties have been applied.
        requestAnimationFrame(draw);
    });
});
</script>
@endpush

@push('styles')
<style>
    .report-kpi {
        border-left: 3px solid var(--color-primary-subtle, rgba(31, 42, 68, 0.08));
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .report-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
    }

    .report-kpi__label {
        font-size: 0.68rem;
        letter-spacing: 0.04em;
    }

    .report-kpi__icon {
        font-size: 0.95rem;
        opacity: 0.85;
    }

    .report-kpi.is-active {
        border-left-color: var(--color-accent);
        background: var(--color-accent-subtle, #FDF1ED);
    }

    /* Sticky header inside the scrollable daily breakdown panel */
    .report-table-head th {
        background-color: var(--color-surface, #fff);
        border-bottom: 1px solid var(--color-border, #e0e0e0);
    }

    [data-theme="dark"] .report-table-head th {
        background-color: var(--color-surface-subtle, #1F2839);
    }
</style>
@endpush