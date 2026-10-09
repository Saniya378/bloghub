@extends('layouts.dashboard')

@section('title', 'Comments — Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Comments on My Posts</h1>
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Comment</th>
                    <th>Thread</th>
                    <th>On Post</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $comment)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $comment->user->avatar }}" alt="" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                            <span class="fw-semibold">{{ $comment->user->name }}</span>
                        </div>
                    </td>
                    <td>{{ Str::limit($comment->content, 80) }}</td>
                    <td>
                        @if($comment->parent_id)
                        <span class="badge bg-secondary" title="Level {{ $comment->depth() }} in the thread">
                            <i class="fa-solid fa-reply me-1"></i>Level {{ $comment->depth() }}
                        </span>
                        <small class="text-muted d-block mt-1">
                            to {{ $comment->parent?->user?->name ?? 'a removed comment' }}
                        </small>
                        @else
                        <span class="badge bg-light text-dark"><i class="fa-solid fa-comment me-1"></i>Top level</span>
                        @endif

                        @if($comment->replies_count > 0)
                        <small class="text-muted d-block mt-1">
                            <i class="fa-solid fa-layer-group me-1"></i>{{ $comment->replies_count }} {{ $comment->replies_count === 1 ? 'reply' : 'replies' }}
                        </small>
                        @endif
                    </td>
                    <td><a href="{{ url('/blogs/' . $comment->article->slug) }}#comment-{{ $comment->id }}" target="_blank">{{ Str::limit($comment->article->title, 30) }}</a></td>
                    <td><span class="badge {{ $comment->status === 'approved' ? 'bg-success' : ($comment->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">{{ ucfirst($comment->status) }}</span></td>
                    <td><x-time-ago :date="$comment->created_at" /></td>
                    <td>
                        <div class="d-flex gap-2">
                            @if($comment->status !== 'approved')
                            <form method="POST" action="{{ url('/dashboard/comments/' . $comment->id . '/approve') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve"><i class="fa-solid fa-check"></i></button>
                            </form>
                            @endif
                            <form method="POST" action="{{ url('/dashboard/comments/' . $comment->id . '/delete') }}"
                                  onsubmit="return {{ $comment->replies_count > 0 ? 'confirm(\'This will also delete ' . $comment->replies_count . ' nested ' . ($comment->replies_count === 1 ? 'reply' : 'replies') . '. Continue?\')' : 'true' }};">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-comments fa-2x mb-3 d-block"></i>
                        No comments found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($comments->hasPages())
    <div class="card-footer">
        {{ $comments->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
