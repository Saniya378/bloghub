@extends('layouts.dashboard')

@section('title', 'Articles by Author Report')
@section('report_heading', 'Articles Grouped by Author')
@section('report_subheading', 'Comparative analysis of content generation across individual authors.')

@section('report_actions')
<a href="{{ route('admin.reports.authors.export.activity') }}" class="btn btn-sm btn-bh btn-bh-primary">
    <i class="fa-solid fa-file-csv me-1"></i> Export Author Articles CSV
</a>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('pages.admin.reports.header')

    <!-- Search filter -->
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.articles.authors') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold mb-1">Search Author</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search author by name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->filled('search'))
                    <a href="{{ route('admin.reports.articles.authors') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Articles by Author (PDF Section 4.C) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title fw-bold mb-0" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-user-pen text-primary me-2"></i>Author Content Contributions
            </h5>
            <span class="badge bg-light text-dark border">{{ $authors->total() }} Authors</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">Rank</th>
                        <th>Author</th>
                        <th class="text-center">Total Articles</th>
                        <th class="text-center">Published</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Rejected</th>
                        <th class="text-end">Total Views</th>
                        <th class="text-end">Avg Views / Post</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($authors as $idx => $author)
                    @php
                        $avgViews = $author->published_count > 0 ? round(($author->total_views ?? 0) / $author->published_count) : 0;
                    @endphp
                    <tr>
                        <td class="text-center">
                            <span class="badge {{ $idx === 0 ? 'bg-warning text-dark' : ($idx === 1 ? 'bg-secondary' : ($idx === 2 ? 'bg-bronze text-white' : 'bg-light text-dark border')) }} rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-family: var(--font-meta);">
                                {{ $authors->firstItem() + $idx }}
                            </span>
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
                            {{ $author->total_articles }}
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
                        <td class="text-end text-muted" style="font-family: var(--font-meta);">
                            {{ number_format($avgViews) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No authors found.</td>
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

@push('styles')
<style>
    .bg-bronze { background-color: #cd7f32; }
</style>
@endpush
@endsection
