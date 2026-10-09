@extends('layouts.dashboard')

@section('title', 'My Posts — Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">My Posts</h1>
    <a href="{{ url('/dashboard/posts/create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>New Post</a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search posts..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>In Review</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Posts Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Comments</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $post->image }}" alt="" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <div class="fw-semibold">{{ Str::limit($post->title, 40) }}</div>
                                <small class="text-muted">{{ $post->reading_time }}</small>
                                @if($post->isRejected() && $post->review_note)
                                    <small class="text-danger d-block mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ Str::limit($post->review_note, 70) }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark">{{ ucfirst($post->category_slug) }}</span></td>
                    <td>
                        <span class="badge {{ $post->statusBadgeClass() }}">{{ $post->statusLabel() }}</span>
                        @if($post->isPending() && $post->submitted_at)
                            <small class="text-muted d-block mt-1"><x-time-ago :date="$post->submitted_at" prefix="Sent " /></small>
                        @endif
                    </td>
                    <td>{{ number_format($post->views) }}</td>
                    <td>{{ $post->comments_count }}</td>
                    <td>{{ $post->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/blogs/' . $post->slug) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ url('/dashboard/posts/' . $post->id . '/edit') }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-edit"></i></a>
                            <button class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $post->id }}"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $post->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Post</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete "<strong>{{ $post->title }}</strong>"? This action cannot be undone.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ url('/dashboard/posts/' . $post->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-inbox fa-2x mb-3 d-block"></i>
                        No posts found. <a href="{{ url('/dashboard/posts/create') }}">Create your first post</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="card-footer">
        {{ $posts->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
