@extends('layouts.dashboard')

@section('title', 'Manage Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Manage Posts</h1>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search posts..." value="{{ request('search') }}">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
        </select>
        <select name="category" class="form-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach(['technology', 'education', 'travel', 'food', 'business', 'sports', 'health', 'lifestyle', 'finance', 'science', 'art', 'culture'] as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline-primary">Filter</button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Views</th>
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
                                <small class="text-muted">{{ $post->comments_count }} comments</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $post->user->name }}</td>
                    <td><span class="badge bg-light text-dark">{{ ucfirst($post->category_slug) }}</span></td>
                    <td><span class="badge {{ $post->status === 'published' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($post->status) }}</span></td>
                    <td>
                        <form method="POST" action="{{ url('/admin/posts/' . $post->id . '/featured') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $post->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="{{ $post->is_featured ? 'fa-solid fa-star' : 'fa-regular fa-star' }}"></i>
                            </button>
                        </form>
                    </td>
                    <td>{{ number_format($post->views) }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/blogs/' . $post->slug) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fa-solid fa-eye"></i></a>
                            @if($post->status === 'published')
                            <form method="POST" action="{{ url('/admin/posts/' . $post->id . '/unpublish') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Unpublish"><i class="fa-solid fa-undo"></i></button>
                            </form>
                            @endif
                            <button class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $post->id }}"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <div class="modal fade" id="deleteModal{{ $post->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Post</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">Are you sure you want to delete "<strong>{{ $post->title }}</strong>"?</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ url('/admin/posts/' . $post->id . '/delete') }}">
                                            @csrf
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
                    <td colspan="7" class="text-center py-5 text-muted">No posts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="card-footer">{{ $posts->withQueryString()->links() }}</div>
    @endif
</div>
