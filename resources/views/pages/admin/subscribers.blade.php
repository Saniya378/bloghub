@extends('layouts.dashboard')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Newsletter Subscribers</h1>
    <a href="{{ url('/admin/subscribers/export') }}" class="btn btn-success"><i class="fa-solid fa-download me-2"></i>Export CSV</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Subscribed At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $subscriber)
                <tr>
                    <td class="fw-semibold">{{ $subscriber->email }}</td>
                    <td>{{ $subscriber->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center py-5 text-muted">No subscribers yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subscribers->hasPages())
    <div class="card-footer">{{ $subscribers->links() }}</div>
    @endif
</div>
@endsection
