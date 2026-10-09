@extends('layouts.dashboard')

@section('title', 'Notifications — Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Notifications</h1>
    @if(auth()->user()->unreadNotifications()->exists())
    <form method="POST" action="{{ route('notifications.readAll') }}">
        @csrf
        <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-check-double me-2"></i>Mark all as read</button>
    </form>
    @endif
</div>

<div class="card">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
        @php
            $data = $notification->data;
            $isRejected = $notification->type === App\Notifications\PostRejected::class;
            $presentation = $isRejected
                ? App\Notifications\PostRejected::presentation()
                : App\Notifications\PostApproved::presentation();
            $url = $isRejected
                ? route('dashboard.posts.edit', $data['article_id'])
                : url('/blogs/' . $data['slug']);
        @endphp
        <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
            <div class="d-flex gap-3 align-items-start">
                <i class="{{ $presentation['icon'] }} {{ $presentation['color'] }} fs-5 mt-1"></i>
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ Str::limit($data['title'] ?? 'Notification', 80) }}</div>
                    <div class="small text-muted mb-1">{{ $data['message'] ?? '' }}</div>
                    @if(!empty($data['note']))
                    <div class="small border-start border-3 ps-2 mb-1" style="border-color: var(--color-primary) !important;">
                        <span class="text-muted">Note from {{ $data['admin_name'] }}:</span>
                        {{ $data['note'] }}
                    </div>
                    @endif
                    <div class="small text-muted">
                        <x-time-ago :date="$notification->created_at" />
                        @if($data['admin_name'] ?? null)
                        &middot; by {{ $data['admin_name'] }}
                        @endif
                    </div>
                </div>
                <div class="d-flex flex-column gap-2 text-nowrap">
                    <a href="{{ $url }}" class="btn btn-sm btn-outline-primary">
                        {{ $isRejected ? 'Edit post' : 'View post' }}
                    </a>
                    @if(!$notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Mark read</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="fa-regular fa-bell-slash fa-3x mb-3 d-block"></i>
            <p class="mb-0">Nothing here yet.</p>
            <small>You will be notified when an admin reviews your posts.</small>
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection