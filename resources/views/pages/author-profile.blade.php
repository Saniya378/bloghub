@extends('layouts.app')

@section('title', $author->name . ' — Author Profile on BlogHub')
@section('meta_description', $author->bio)

@section('content')
<!-- Profile Header -->
<section class="position-relative mb-5">
    <div style="height: 250px; background-image: url('{{ $author->cover_image }}'); background-size: cover; background-position: center;"></div>
    <div class="container position-relative" style="margin-top: -75px;">
        <div class="d-flex align-items-end gap-4">
            <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="rounded-circle border border-4 border-white shadow" style="width: 150px; height: 150px; object-fit: cover;">
            <div class="pb-3">
                <h1 class="h2 fw-bold mb-1">{{ $author->name }}</h1>
                <p class="text-primary fw-semibold mb-2">{{ $author->tagline }}</p>
                <div class="d-flex gap-3">
                    @if(!empty($author->social['twitter']))
                    <a href="{{ $author->social['twitter'] }}" target="_blank" class="text-muted"><i class="fa-brands fa-twitter fa-lg"></i></a>
                    @endif
                    @if(!empty($author->social['github']))
                    <a href="{{ $author->social['github'] }}" target="_blank" class="text-muted"><i class="fa-brands fa-github fa-lg"></i></a>
                    @endif
                    @if(!empty($author->social['linkedin']))
                    <a href="{{ $author->social['linkedin'] }}" target="_blank" class="text-muted"><i class="fa-brands fa-linkedin fa-lg"></i></a>
                    @endif
                </div>
            </div>
            <div class="ms-auto pb-3">
                <button class="btn btn-primary btn-lg follow-btn" data-author-id="{{ $author->id }}">
                    <i class="fa-solid fa-plus me-1"></i> Follow
                </button>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <!-- Bio -->
                <div class="mb-5">
                    <h2 class="h4 fw-bold mb-3">About</h2>
                    <p class="text-muted">{{ $author->bio }}</p>
                </div>

                <!-- Topic Tags -->
                @if(!empty($topicTags))
                <div class="mb-5">
                    <h2 class="h4 fw-bold mb-3">Topics</h2>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($topicTags as $tag)
                        <a href="{{ url('/blogs?category=' . $tag) }}" class="badge bg-light text-dark text-decoration-none">{{ ucfirst($tag) }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Published Posts -->
                <div>
                    <h2 class="h4 fw-bold mb-4">Articles ({{ $posts->total }})</h2>
                    <div class="row g-4">
                        @foreach($posts as $post)
                        <div class="col-md-6">
                            <div class="card h-100">
                                <a href="{{ url('/blogs/' . $post->slug) }}" class="text-decoration-none">
                                    <img src="{{ $post->image }}" alt="{{ $post->title }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary">{{ ucfirst($post->category_slug) }}</span>
                                        <small class="text-muted">{{ $post->reading_time }}</small>
                                    </div>
                                    <h3 class="card-title h6">
                                        <a href="{{ url('/blogs/' . $post->slug) }}" class="text-decoration-none text-dark">{{ $post->title }}</a>
                                    </h3>
                                    <p class="card-text text-muted small flex-grow-1">{{ Str::limit($post->excerpt, 80) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                        <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                        <small class="text-muted"><i class="fa-regular fa-eye me-1"></i>{{ number_format($post->views) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($posts->hasPages())
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            @if($posts->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $posts->previousPageUrl() }}">Previous</a></li>
                            @endif
                            @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $posts->currentPage() ? 'active' : '' }}"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endforeach
                            @if($posts->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $posts->nextPageUrl() }}">Next</a></li>
                            @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                            @endif
                        </ul>
                    </nav>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    <!-- Stats -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h6 fw-bold mb-3">Statistics</h3>
                            <div class="row g-3 text-center">
                                <div class="col-6">
                                    <div class="h4 fw-bold text-primary">{{ $stats['articles'] }}</div>
                                    <small class="text-muted">Articles</small>
                                </div>
                                <div class="col-6">
                                    <div class="h4 fw-bold text-primary">{{ $stats['followers'] }}</div>
                                    <small class="text-muted">Followers</small>
                                </div>
                                <div class="col-6">
                                    <div class="h4 fw-bold text-primary">{{ $stats['following'] }}</div>
                                    <small class="text-muted">Following</small>
                                </div>
                                <div class="col-6">
                                    <div class="h4 fw-bold text-primary">{{ number_format($stats['total_views']) }}</div>
                                    <small class="text-muted">Total Views</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Specialty -->
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h6 fw-bold mb-2">Specialty</h3>
                            <p class="text-muted mb-0">{{ $author->specialty }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const followBtn = document.querySelector('.follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', async () => {
            const authorId = followBtn.dataset.authorId;
            try {
                const response = await fetch(`/authors/${authorId}/follow`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                if (data.success) {
                    followBtn.innerHTML = data.following ? '<i class="fa-solid fa-check me-1"></i> Following' : '<i class="fa-solid fa-plus me-1"></i> Follow';
                    followBtn.classList.toggle('btn-primary', !data.following);
                    followBtn.classList.toggle('btn-outline-primary', data.following);
                }
            } catch (error) {
                showToast('Error', 'Failed to follow author.', 'fa-solid fa-exclamation-circle text-danger');
            }
        });
    }
});
</script>
@endpush
