{{-- Admin Reports Navigation Header --}}
<div class="reports-nav-wrapper mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1" style="font-family: var(--font-meta); font-size: 0.75rem;">
                    <i class="fa-solid fa-chart-pie me-1"></i> ADMIN INTELLIGENCE
                </span>
                <span class="text-muted small">&bull; Real-time Analytics</span>
            </div>
            <h1 class="h3 fw-bold mb-0 text-dark" style="font-family: var(--font-heading);">
                @yield('report_heading', 'Reports & Analytics')
            </h1>
            <p class="text-muted small mb-0 mt-1">@yield('report_subheading', 'Comprehensive analytics, audit logs and operational metrics for BlogHub.')</p>
        </div>
        <div class="d-flex flex-wrap gap-2 d-print-none">
            @yield('report_actions')
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-print"></i> <span>Print Report</span>
            </button>
        </div>
    </div>

    {{-- Sub-navigation Tabs --}}
    <div class="reports-tabs-bar d-flex flex-wrap gap-2 mt-3 d-print-none">
        <a href="{{ route('admin.reports.index') }}" 
           class="btn btn-sm {{ request()->routeIs('admin.reports.index') ? 'btn-primary active fw-semibold shadow-sm' : 'btn-outline-secondary' }}">
            <i class="fa-solid fa-chart-line me-1"></i> Overview
        </a>
        
        <div class="dropdown d-inline-block">
            <button class="btn btn-sm dropdown-toggle {{ request()->routeIs('admin.reports.authors*') ? 'btn-primary active fw-semibold shadow-sm' : 'btn-outline-secondary' }}" 
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-users me-1"></i> Authors & Users
            </button>
            <ul class="dropdown-menu shadow-sm">
                <li><a class="dropdown-item {{ request()->routeIs('admin.reports.authors') && !request()->routeIs('admin.reports.authors.activity') ? 'active' : '' }}" href="{{ route('admin.reports.authors') }}"><i class="fa-solid fa-user-check me-2"></i> User Registrations</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.reports.authors.activity') ? 'active' : '' }}" href="{{ route('admin.reports.authors.activity') }}"><i class="fa-solid fa-fire me-2"></i> Author Activity & Productivity</a></li>
            </ul>
        </div>

        <div class="dropdown d-inline-block">
            <button class="btn btn-sm dropdown-toggle {{ request()->routeIs('admin.reports.articles*') ? 'btn-primary active fw-semibold shadow-sm' : 'btn-outline-secondary' }}" 
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-file-lines me-1"></i> Articles & Content
            </button>
            <ul class="dropdown-menu shadow-sm">
                <li><a class="dropdown-item {{ request()->routeIs('admin.reports.articles') && !request()->routeIs('admin.reports.articles.*') ? 'active' : '' }}" href="{{ route('admin.reports.articles') }}"><i class="fa-solid fa-list me-2"></i> Article Summary & Status</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.reports.articles.authors') ? 'active' : '' }}" href="{{ route('admin.reports.articles.authors') }}"><i class="fa-solid fa-user-pen me-2"></i> Articles by Author</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.reports.articles.categories') ? 'active' : '' }}" href="{{ route('admin.reports.articles.categories') }}"><i class="fa-solid fa-folder-tree me-2"></i> Articles by Category</a></li>
            </ul>
        </div>

        <div class="dropdown d-inline-block">
            <button class="btn btn-sm dropdown-toggle {{ request()->routeIs('admin.reports.approvals*') ? 'btn-primary active fw-semibold shadow-sm' : 'btn-outline-secondary' }}" 
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-shield-halved me-1"></i> Approvals
            </button>
            <ul class="dropdown-menu shadow-sm">
                <li><a class="dropdown-item {{ request()->routeIs('admin.reports.approvals.articles') ? 'active' : '' }}" href="{{ route('admin.reports.approvals.articles') }}"><i class="fa-solid fa-clipboard-check me-2"></i> Article Approvals</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.reports.approvals.authors') ? 'active' : '' }}" href="{{ route('admin.reports.approvals.authors') }}"><i class="fa-solid fa-user-shield me-2"></i> Author Status Approvals</a></li>
            </ul>
        </div>

        <a href="{{ route('admin.reports.engagement.views') }}" 
           class="btn btn-sm {{ request()->routeIs('admin.reports.engagement*') ? 'btn-primary active fw-semibold shadow-sm' : 'btn-outline-secondary' }}">
            <i class="fa-solid fa-arrow-trend-up me-1"></i> Engagement & Views
        </a>

        <a href="{{ route('admin.reports.activity') }}" 
           class="btn btn-sm {{ request()->routeIs('admin.reports.activity*') ? 'btn-primary active fw-semibold shadow-sm' : 'btn-outline-secondary' }}">
            <i class="fa-solid fa-clock-rotate-left me-1"></i> Activity / Audit Log
        </a>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body {
            background-color: #fff !important;
            color: #000 !important;
        }
        .dashboard-sidebar, .dashboard-topbar, .reports-tabs-bar, .d-print-none, .btn, .pagination {
            display: none !important;
        }
        .dashboard-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .table {
            color: #000 !important;
        }
    }
</style>
@endpush
