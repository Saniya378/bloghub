@extends('layouts.dashboard')

@section('title', 'Contact Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Contact Messages</h1>
    <form method="GET" class="d-flex gap-2">
        <select name="read" class="form-select" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="unread" {{ request('read') == 'unread' ? 'selected' : '' }}>Unread</option>
            <option value="read" {{ request('read') == 'read' ? 'selected' : '' }}>Read</option>
        </select>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $message)
                <tr class="{{ !$message->is_read ? 'table-warning' : '' }}">
                    <td>
                        <div class="fw-semibold">{{ $message->name }}</div>
                        <small class="text-muted">{{ $message->email }}</small>
                    </td>
                    <td class="fw-semibold">{{ $message->subject }}</td>
                    <td>{{ Str::limit($message->message, 60) }}</td>
                    <td>
                        @if($message->is_read)
                        <span class="badge bg-success">Read</span>
                        @else
                        <span class="badge bg-warning">Unread</span>
                        @endif
                    </td>
                    <td>{{ $message->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            @if(!$message->is_read)
                            <form method="POST" action="{{ url('/admin/messages/' . $message->id . '/read') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark Read"><i class="fa-solid fa-check"></i></button>
                            </form>
                            @endif
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $message->id }}"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <div class="modal fade" id="deleteModal{{ $message->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Message</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">Are you sure you want to delete this message?</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ url('/admin/messages/' . $message->id . '/delete') }}">
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
                    <td colspan="6" class="text-center py-5 text-muted">No messages found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
    <div class="card-footer">{{ $messages->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
