@extends('layouts.dashboard')

@section('title', 'Author Approval Report')
@section('report_heading', 'Author Onboarding & Status Approvals')
@section('report_subheading', 'Monitoring author applications, role upgrades, verification status, and audit trails.')

@section('report_actions')
<a href="{{ route('admin.reports.authors.export.registrations') }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Author Log
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Metrics Row (PDF Section 5.A) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Avg Author Turnaround</div>
                    <div class="h4 fw-bold my-1 text-primary" style="font-family: var(--font-meta);">{{ $stats['avg_approval_time'] }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-bolt me-1"></i>Application to Author</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Active Authors</div>
                    <div class="h4 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($stats['active_authors']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-check-circle me-1"></i>Verified Writers</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Potential Authors</div>
                    <div class="h4 fw-bold my-1 text-warning" style="font-family: var(--font-meta);">{{ number_format($stats['pending_readers']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-user-group me-1"></i>Readers Base</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Restricted Accounts</div>
                    <div class="h4 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($stats['blocked_authors']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-ban me-1"></i>Suspended Authors</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Author List & Decisions Table (PDF Section 5.A) -->
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                        <i class="fa-solid fa-users-gear text-primary me-2"></i>Author Verification Directory
                    </h5>
                    <span class="badge bg-light text-dark border">{{ $authors->total() }} Authors</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Author</th>
                                <th>Joined Date</th>
                                <th>Decision</th>
                                <th>Status</th>
                                <th class="text-center">Articles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($authors as $author)
                            <tr>
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
                                <td style="font-family: var(--font-meta); font-size: 0.8125rem;">
                                    {{ $author->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fa-solid fa-check me-1"></i>Approved Author</span>
                                </td>
                                <td>
                                    @if($author->is_active)
                                        <span class="badge bg-success text-white px-2 py-1">Active</span>
                                    @else
                                        <span class="badge bg-danger text-white px-2 py-1">Blocked</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold" style="font-family: var(--font-meta);">
                                    {{ $author->articles_count }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No authors registered.</td>
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

        <!-- Recent Audit Events for Authors -->
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                        <i class="fa-solid fa-shield-halved text-secondary me-2"></i>Recent Author Audit Log
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($approvalLogs as $log)
                        <li class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary-subtle text-primary">{{ $log->action }}</span>
                                <small class="text-muted" style="font-family: var(--font-meta); font-size: 0.75rem;">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="small text-dark mb-1">{{ $log->description }}</p>
                            <small class="text-muted"><i class="fa-solid fa-user-shield me-1"></i>{{ $log->user?->name ?? 'Admin' }}</small>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-4">No author approval log records.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
