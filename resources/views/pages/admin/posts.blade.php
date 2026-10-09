@extends('layouts.dashboard')

@section('title', 'Manage Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Manage Posts</h1>
        @php $reviewCount = \App\Models\Article::pending()->count(); @endphp
        @if($reviewCount > 0)
        <a href="{{ route('admin.posts', ['status' => 'pending']) }}" class="small text-decoration-none">
            <i class="fa-solid fa-hourglass-half me-1"></i>{{ $reviewCount }} post{{ $reviewCount === 1 ? '' : 's' }} waiting for your review
        </a>
        @endif
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search posts..." value="{{ request('search') }}">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>In Review ({{ \App\Models\Article::pending()->count() }})</option>
            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                    <td>{{ $post->user?->name ?? 'Unknown' }}</td>
                    <td><span class="badge bg-light text-dark">{{ ucfirst($post->category_slug) }}</span></td>
                    <td>
                        <span class="badge {{ $post->statusBadgeClass() }}">{{ $post->statusLabel() }}</span>
                        @if($post->isPending() && $post->submitted_at)
                        <small class="text-muted d-block mt-1"><x-time-ago :date="$post->submitted_at" prefix="Submitted " /></small>
                        @elseif($post->reviewed_at)
                        <small class="text-muted d-block mt-1">by {{ $post->reviewer?->name ?? 'admin' }}</small>
                        @endif
                    </td>
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
                            <a href="{{ url('/blogs/' . $post->slug) }}" class="btn btn-sm btn-outline-info" title="{{ $post->isPending() ? 'Preview' : 'View' }}"><i class="fa-solid fa-eye"></i></a>
                            @if($post->isPending())
                            <form method="POST" action="{{ route('admin.posts.approve', $post->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Approve &amp; publish"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <button class="btn btn-sm btn-outline-danger" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $post->id }}"><i class="fa-solid fa-xmark"></i></button>
                            @elseif($post->isPublished())
                            <form method="POST" action="{{ route('admin.posts.unpublish', $post->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Unpublish"><i class="fa-solid fa-undo"></i></button>
                            </form>
                            @endif
                            <button class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $post->id }}"><i class="fa-solid fa-trash"></i></button>
                        </div>

                        @if($post->isRejected() && $post->review_note)
                        <small class="text-danger d-block mt-2"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $post->review_note }}</small>
                        @endif

                        <!-- Reject Modal -->
                        @if($post->isPending())
                        <div class="modal fade" id="rejectModal{{ $post->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.posts.reject', $post->id) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Post</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-2">Send "<strong>{{ $post->title }}</strong>" back to the author?</p>
                                            <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                                            <textarea name="review_note" class="form-control" rows="3" maxlength="1000" placeholder="Explain what needs to be fixed..." required></textarea>
                                            @error('review_note') <div class="text-danger small">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject Post</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
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
@endsection
