@extends('layouts.dashboard')

@section('title', 'Admin Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Admin Overview</h1>
        <p class="text-muted mb-0">Platform statistics and management.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-primary">{{ $stats['users'] }}</div>
                <small class="text-muted">Total Users</small>
                <hr>
                <div class="d-flex justify-content-around small">
                    <span><strong>{{ $stats['authors'] }}</strong> authors</span>
                    <span><strong>{{ $stats['readers'] }}</strong> readers</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-success">{{ $stats['posts'] }}</div>
                <small class="text-muted">Total Posts</small>
                <hr>
                <div class="d-flex justify-content-around small">
                    <span><strong>{{ $stats['published'] }}</strong> published</span>
                    <span><strong>{{ $stats['drafts'] }}</strong> drafts</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-warning">{{ $stats['pending_comments'] }}</div>
                <small class="text-muted">Pending Comments</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-info">{{ $stats['subscribers'] }}</div>
                <small class="text-muted">Newsletter Subscribers</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-danger">{{ $stats['unread_messages'] }}</div>
                <small class="text-muted">Unread Messages</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-6 fw-bold text-secondary">{{ $stats['testimonials'] }}</div>
                <small class="text-muted">Testimonials</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-4 mt-4">
    <div class="col-md-6 col-lg-4">
        <a href="{{ url('/admin/users') }}" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fa-solid fa-users fa-2x text-primary"></i>
                <div>
                    <h5 class="mb-1 text-dark">Manage Users</h5>
                    <small class="text-muted">List, search, change roles, activate/deactivate</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ url('/admin/posts') }}" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fa-solid fa-file-lines fa-2x text-success"></i>
                <div>
                    <h5 class="mb-1 text-dark">Manage Posts</h5>
                    <small class="text-muted">Filter, feature, unpublish, delete</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ url('/admin/categories') }}" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fa-solid fa-folder-open fa-2x text-warning"></i>
                <div>
                    <h5 class="mb-1 text-dark">Manage Categories</h5>
                    <small class="text-muted">Full CRUD with icon class</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ url('/admin/comments') }}" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fa-solid fa-comments fa-2x text-info"></i>
                <div>
                    <h5 class="mb-1 text-dark">Moderate Comments</h5>
                    <small class="text-muted">Approve or delete comments</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ url('/admin/testimonials') }}" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fa-solid fa-quote-left fa-2x text-secondary"></i>
                <div>
                    <h5 class="mb-1 text-dark">Testimonials</h5>
                    <small class="text-muted">CRUD testimonials</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ url('/admin/messages') }}" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fa-solid fa-envelope fa-2x text-danger"></i>
                <div>
                    <h5 class="mb-1 text-dark">Contact Messages</h5>
                    <small class="text-muted">List, mark read, delete</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="{{ url('/admin/subscribers') }}" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fa-solid fa-newspaper fa-2x text-primary"></i>
                <div>
                    <h5 class="mb-1 text-dark">Newsletter Subscribers</h5>
                    <small class="text-muted">List and export CSV</small>
                </div>
            </div>
        </a>
    </div>
</div>
