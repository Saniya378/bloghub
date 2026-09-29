@extends('layouts.dashboard')

@section('title', 'Manage Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Manage Users</h1>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
        <select name="role" class="form-select" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="author" {{ request('role') == 'author' ? 'selected' : '' }}>Author</option>
            <option value="reader" {{ request('role') == 'reader' ? 'selected' : '' }}>Reader</option>
        </select>
        <button type="submit" class="btn btn-outline-primary">Filter</button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $user->avatar }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <form method="POST" action="{{ url('/admin/users/' . $user->id . '/role') }}" class="d-inline">
                            @csrf
                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="author" {{ $user->role == 'author' ? 'selected' : '' }}>Author</option>
                                <option value="reader" {{ $user->role == 'reader' ? 'selected' : '' }}>Reader</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ url('/admin/users/' . $user->id . '/toggle') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @else
                        <span class="text-muted small">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->withQueryString()->links() }}</div>
    @endif
</div>
