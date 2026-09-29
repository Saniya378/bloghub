@props(['author'])

<div class="col-6 col-md-4 col-lg-2">
    <a href="{{ route('authors.show', $author->username) }}" class="text-decoration-none">
        <div class="author-card text-center p-3">
            <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
            <h3 class="h6 fw-semibold text-dark mb-1">{{ $author->name }}</h3>
            <p class="text-muted small mb-2">{{ $author->tagline }}</p>
            <span class="badge bg-light text-dark">{{ $author->articles_count }} articles</span>
        </div>
    </a>
</div>
