@extends('layouts.dashboard')

@section('title', 'Author Registration Report')
@section('report_heading', 'Author & User Registrations')
@section('report_subheading', 'Directory of registered platform users, author status progression, and onboarding metrics.')

@section('report_actions')
<a href="{{ route('admin.reports.authors.export.registrations', request()->query()) }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Statistics Badges (PDF Section 3.A) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Total Registrations</div>
                    <div class="h3 fw-bold my-1 text-primary" style="font-family: var(--font-meta);">{{ number_format($stats['total_registrations']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-users me-1"></i>All account signups</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Approved Authors</div>
                    <div class="h3 fw-bold my-1 text-success" style="font-family: var(--font-meta);">{{ number_format($stats['active_authors']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-check-circle me-1"></i>Active content creators</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Pending / Readers</div>
                    <div class="h3 fw-bold my-1 text-warning" style="font-family: var(--font-meta);">{{ number_format($stats['pending_readers']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-user-clock me-1"></i>Reader accounts</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-family: var(--font-meta); font-size: 0.75rem;">Blocked / Inactive</div>
                    <div class="h3 fw-bold my-1 text-danger" style="font-family: var(--font-meta);">{{ number_format($stats['blocked']) }}</div>
                    <div class="small text-muted"><i class="fa-solid fa-ban me-1"></i>Suspended access</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar (PDF Section 8 & 10) -->
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.authors') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Search User / Author</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Status / Role</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active / Approved</option>
                        <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked / Inactive</option>
                        <option value="author" {{ request('status') === 'author' ? 'selected' : '' }}>Role: Author</option>
                        <option value="reader" {{ request('status') === 'reader' ? 'selected' : '' }}>Role: Reader</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Generate
                    </button>
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.reports.authors') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- User Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-table-list text-primary me-2"></i>Registration Records
            </h5>
            <span class="badge bg-light text-dark border">{{ $users->total() }} total entries</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Author / User</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Articles</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                     class="rounded-circle" width="34" height="34" alt="{{ $user->name }}" style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted" style="font-family: var(--font-meta);">{{ '@' . ($user->username ?? 'user') }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="font-family: var(--font-meta); font-size: 0.875rem;">
                            {{ $user->email }}
                        </td>
                        <td style="font-family: var(--font-meta); font-size: 0.875rem;">
                            {{ $user->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'author' ? 'bg-primary' : 'bg-secondary') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fa-solid fa-check-circle me-1"></i>Approved</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="fa-solid fa-ban me-1"></i>Blocked</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bold px-2 py-1" style="font-family: var(--font-meta);">
                                {{ $user->articles_count }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No registration records found for the selected filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-transparent py-3 d-print-none">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
