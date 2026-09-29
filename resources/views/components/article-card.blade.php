@props(['article', 'layout' => 'grid'])

@php
$imageUrl = $article->image ?? 'https://picsum.photos/seed/' . $article->slug . '/800/500';
$avatarUrl = $article->user->avatar ?? 'https://i.pravatar.cc/100?img=1';
$articleUrl = url('/blogs/' . $article->slug);
$categoryUrl = url('/blogs?category=' . $article->category_slug);
@endphp

@if($layout === 'featured')
{{-- Featured: large image with text overlay --}}
<article class="bh-card bh-card-hover h-100">
    <a href="{{ $articleUrl }}" class="text-decoration-none d-block position-relative h-100">
        <div class="bh-card-img-wrap" style="aspect-ratio: auto; height: 100%;">
            <img src="{{ $imageUrl }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
        </div>
        <div class="bh-card-overlay">
            <span class="bh-badge bh-badge-accent mb-2">{{ ucfirst($article->category_slug) }}</span>
            <h3 class="h4 fw-bold text-white mb-1">{{ $article->title }}</h3>
            <span class="bh-caption" style="color: rgba(255, 255, 255, 0.85);">{{ $article->user->name }} &bull; {{ $article->reading_time }}</span>
        </div>
    </a>
</article>
@elseif($layout === 'compact')
{{-- Compact: small horizontal card --}}
<article class="bh-card bh-card-hover flex-grow-1">
    <div class="row g-0 h-100">
        <div class="col-sm-5 bh-card-img-wrap" style="aspect-ratio: auto; min-height: 180px;">
            <img src="{{ $imageUrl }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
        </div>
        <div class="col-sm-7 p-3 p-md-4 d-flex flex-column">
            <span class="bh-badge bh-badge-secondary mb-2 align-self-start">{{ ucfirst($article->category_slug) }}</span>
            <h3 class="bh-card-title bh-line-clamp-2 mb-2" style="font-size: 1.05rem;">
                <a href="{{ $articleUrl }}">{{ $article->title }}</a>
            </h3>
            <p class="bh-card-text mb-2" style="font-size: 0.875rem;">{{ Str::limit($article->excerpt, 90) }}</p>
            <div class="bh-card-meta mb-0 pt-2 mt-auto" style="border-top: 1px solid var(--color-border-subtle);">
                <span>{{ $article->user->name }}</span>
                <span class="ms-auto">{{ $article->reading_time }}</span>
            </div>
        </div>
    </div>
</article>
@else
{{-- Default: normal grid card (fixed 16:9 image) --}}
<article class="bh-card bh-card-hover h-100">
    <a href="{{ $articleUrl }}" class="text-decoration-none d-block">
        <div class="bh-card-img-wrap">
            <img src="{{ $imageUrl }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
        </div>
    </a>
    <div class="bh-card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ $categoryUrl }}" class="bh-badge bh-badge-accent">{{ ucfirst($article->category_slug) }}</a>
            <span class="bh-caption"><i class="fa-regular fa-clock me-1"></i>{{ $article->reading_time }}</span>
        </div>
        <h3 class="bh-card-title mb-2" style="font-size: 1.15rem;">
            <a href="{{ $articleUrl }}">{{ $article->title }}</a>
        </h3>
        <p class="bh-card-text">{{ Str::limit($article->excerpt, 100) }}</p>
        <div class="bh-card-meta mb-0 pt-3 mt-auto" style="border-top: 1px solid var(--color-border-subtle);">
            <a href="{{ route('authors.show', $article->user->username) }}" class="d-flex align-items-center gap-2 text-decoration-none" style="color: inherit;">
                <img src="{{ $avatarUrl }}" alt="{{ $article->user->name }}" width="28" height="28" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-placeholder.svg') }}'">
                <span class="fw-semibold" style="color: var(--color-primary);">{{ $article->user->name }}</span>
            </a>
            <span class="ms-auto">{{ $article->published_at->diffForHumans() }}</span>
        </div>
    </div>
</article>
@endif
