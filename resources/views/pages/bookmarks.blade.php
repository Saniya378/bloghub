@extends('layouts.app')

@section('title', 'My Bookmarks — BlogHub')
@section('meta_description', 'Your saved articles and reading list on BlogHub.')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="mb-5">
            <span class="text-primary fw-semibold">Reading List</span>
            <h1 class="h2 fw-bold">My Bookmarks</h1>
            <p class="text-muted">Articles you've saved for later reading.</p>
        </div>

        @if($bookmarks->isNotEmpty())
        <div class="row g-4">
            @foreach($bookmarks as $bookmark)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <a href="{{ url('/blogs/' . $bookmark->article->slug) }}" class="text-decoration-none">
                        <img src="{{ $bookmark->article->image }}" alt="{{ $bookmark->article->title }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ ucfirst($bookmark->article->category_slug) }}</span>
                            <small class="text-muted">{{ $bookmark->article->reading_time }}</small>
                        </div>
                        <h3 class="card-title h6">
                            <a href="{{ url('/blogs/' . $bookmark->article->slug) }}" class="text-decoration-none text-dark">{{ $bookmark->article->title }}</a>
                        </h3>
                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($bookmark->article->excerpt, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <img src="{{ $bookmark->article->user->avatar }}" alt="{{ $bookmark->article->user->name }}" class="rounded-circle me-2" style="width: 28px; height: 28px; object-fit: cover;">
                                <small class="fw-semibold">{{ $bookmark->article->user->name }}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" data-action="bookmark" data-id="{{ $bookmark->article->id }}">
                                <i class="fa-solid fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($bookmarks->hasPages())
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                @if($bookmarks->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                @else
                <li class="page-item"><a class="page-link" href="{{ $bookmarks->previousPageUrl() }}">Previous</a></li>
                @endif

                @foreach($bookmarks->getUrlRange(1, $bookmarks->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $bookmarks->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                @if($bookmarks->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $bookmarks->nextPageUrl() }}">Next</a></li>
                @else
                <li class="page-item disabled"><span class="page-link">Next</span></li>
                @endif
            </ul>
        </nav>
        @endif
        @else
        <div class="text-center py-5">
            <i class="fa-solid fa-bookmark fa-3x text-muted mb-3"></i>
            <h3 class="h5">No bookmarks yet</h3>
            <p class="text-muted">Save articles to read later by clicking the bookmark icon.</p>
            <a href="{{ url('/blogs') }}" class="btn btn-primary">Explore Articles</a>
        </div>
        @endif
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Include toggle-actions.js functionality
    if (typeof ToggleActions !== 'undefined') {
        ToggleActions.init();
    }
});
</script>
@endpush
