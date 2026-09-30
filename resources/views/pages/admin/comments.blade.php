@extends('layouts.dashboard')

@section('title', 'Moderate Comments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Moderate Comments</h1>
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
                    <td><a href="{{ url('/blogs/' . $comment->article->slug) }}" target="_blank">{{ Str::limit($comment->article->title, 30) }}</a></td>
                    <td><span class="badge {{ $comment->status === 'approved' ? 'bg-success' : ($comment->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">{{ ucfirst($comment->status) }}</span></td>
                    <td>{{ $comment->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            @if($comment->status !== 'approved')
                            <form method="POST" action="{{ url('/admin/comments/' . $comment->id . '/approve') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve"><i class="fa-solid fa-check"></i></button>
                            </form>
                            @endif
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $comment->id }}"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <div class="modal fade" id="deleteModal{{ $comment->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Comment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">Are you sure you want to delete this comment?</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ url('/admin/comments/' . $comment->id . '/delete') }}">
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
                    <td colspan="6" class="text-center py-5 text-muted">No comments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($comments->hasPages())
    <div class="card-footer">{{ $comments->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
