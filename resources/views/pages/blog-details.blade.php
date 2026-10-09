@extends('layouts.app')

@section('title', $article->title . ' — BlogHub')
@section('meta_description', $article->excerpt)
@section('og_type', 'article')
@section('og_title', $article->title)
@section('og_description', $article->excerpt)
@section('og_image', $article->image ? (str_starts_with($article->image, 'http') ? $article->image : asset('storage/' . $article->image)) : asset('assets/images/og-default.jpg'))

@section('content')
<section class="py-4">
    <div class="container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/blogs') }}">Blogs</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/blogs?category=' . $article->category_slug) }}">{{ ucfirst($article->category_slug) }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($article->title, 50) }}</li>
            </ol>
        </nav>

        <!-- Article Header -->
        <div class="mb-4">
            <span class="badge bg-primary mb-2">{{ ucfirst($article->category_slug) }}</span>
            <h1 class="h2 fw-bold mb-3">{{ $article->title }}</h1>
            <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-3">
                <a href="{{ $article->user?->profileUrl() ?? url('/authors') }}" class="d-flex align-items-center text-decoration-none text-dark">
                    @if($article->user?->avatar)
                    <img src="{{ $article->user->avatar }}" alt="{{ $article->user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; background: var(--color-primary); color: white; font-weight: 700;">{{ strtoupper(substr($article->user?->name ?? 'U', 0, 1)) }}</span>
                    @endif
                    <div>
                        <div class="fw-semibold">{{ $article->user?->name ?? 'Unknown author' }}</div>
                        <small class="text-muted">{{ $article->user?->tagline }}</small>
                    </div>
                </a>
                <span><i class="fa-regular fa-calendar me-1"></i>{{ $article->published_at ? $article->published_at->format('M d, Y') : 'Draft' }}</span>
                <span><i class="fa-regular fa-clock me-1"></i>{{ $article->reading_time }}</span>
                <span><i class="fa-regular fa-eye me-1"></i>{{ number_format($article->views) }} views</span>
            </div>
            @if(!empty($article->tags))
            <div class="d-flex flex-wrap gap-2">
                @foreach($article->tags as $tag)
                <span class="badge bg-light text-dark">{{ $tag }}</span>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Featured Image -->
        <div class="mb-5">
            <img src="{{ $article->image }}" alt="{{ $article->title }}" class="img-fluid rounded-4" style="width: 100%; max-height: 500px; object-fit: cover;">
        </div>

        <!-- Article Body -->
        <div class="row g-5">
            <div class="col-lg-8">
                <article class="article-body">
                    {!! $article->content !!}

                    <!-- Image Gallery -->
                    @if($article->images && count($article->images) > 0)
                    <div class="row g-3 my-4">
                        @foreach($article->images as $image)
                        <div class="col-md-4">
                            <a href="{{ $image }}" data-lightbox="gallery">
                                <img src="{{ $image }}" alt="Gallery image" class="img-fluid rounded" style="cursor: pointer;">
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </article>

                @php
                    $viewer = auth()->user();
                    $hasLiked = $viewer ? $viewer->articleLikes()->where('article_id', $article->id)->exists() : false;
                    $hasBookmarked = $viewer ? $viewer->bookmarks()->where('article_id', $article->id)->exists() : false;
                @endphp

                <!-- Share & Actions -->
                <div class="d-flex align-items-center gap-3 py-4 border-top border-bottom">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-share-nodes me-1"></i> Share
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" target="_blank"><i class="fa-brands fa-twitter me-2"></i>Twitter</a></li>
                            <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"><i class="fa-brands fa-facebook me-2"></i>Facebook</a></li>
                            <li><a class="dropdown-item" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank"><i class="fa-brands fa-linkedin me-2"></i>LinkedIn</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" id="copyLinkBtn"><i class="fa-solid fa-link me-2"></i>Copy Link</button></li>
                        </ul>
                    </div>

                    <button class="btn {{ $hasLiked ? 'btn-danger' : 'btn-outline-danger' }}" id="likeBtn" data-article-id="{{ $article->id }}" aria-pressed="{{ $hasLiked ? 'true' : 'false' }}">
                        <i class="{{ $hasLiked ? 'fa-solid' : 'fa-regular' }} fa-heart me-1"></i>
                        <span id="likeCount">{{ $article->likes }}</span>
                        <span class="visually-hidden">likes</span>
                    </button>

                    <button class="btn {{ $hasBookmarked ? 'btn-primary' : 'btn-outline-primary' }}" id="bookmarkBtn" data-article-id="{{ $article->id }}" aria-pressed="{{ $hasBookmarked ? 'true' : 'false' }}">
                        <i class="{{ $hasBookmarked ? 'fa-solid' : 'fa-regular' }} fa-bookmark me-1"></i>
                        <span id="bookmarkLabel">{{ $hasBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
                    </button>
                </div>

                <!-- Author Box -->
                @php
                    $author = $article->user;
                    $authorUrl = $author?->profileUrl();
                @endphp
                <div class="card my-4">
                    <div class="card-body d-flex gap-4">
                        @if($author?->avatar)
                        <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 80px; height: 80px; background: var(--color-primary); color: white; font-weight: 700; font-size: 2rem;">{{ strtoupper(substr($author?->name ?? 'U', 0, 1)) }}</span>
                        @endif
                        <div>
                            <div class="text-muted small">Written by</div>
                            <h3 class="h5 fw-bold mb-1">
                                @if($authorUrl)
                                <a href="{{ $authorUrl }}" class="text-decoration-none text-dark">{{ $author->name }}</a>
                                @else
                                {{ $author->name ?? 'Unknown author' }}
                                @endif
                            </h3>
                            <p class="text-muted mb-2">{{ $author?->bio }}</p>
                            @if($authorUrl)
                            <a href="{{ $authorUrl }}" class="btn btn-sm btn-outline-primary">View Profile</a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="my-5" id="commentsSection"
                     data-article-id="{{ $article->id }}"
                     data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">
                    <h3 class="h4 fw-bold mb-4">Comments <span id="commentCount">({{ $commentTotal }})</span></h3>

                    @auth
                    <form id="commentForm" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="commentText" class="form-label">Add a comment</label>
                            <textarea class="form-control" id="commentText" name="content" rows="3" placeholder="Share your thoughts..." required maxlength="1000"></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted" id="charCounter">0 / 1000</small>
                                <small class="text-muted" id="rateLimitInfo"></small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="commentSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="commentSpinner"></span>
                            <span id="commentBtnText">Post Comment</span>
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <a href="{{ url('/login') }}">Sign in</a> to like comments and join the discussion.
                    </div>
                    @endauth

                    @include('partials.comment-thread', [
                        'comments' => $commentRoots,
                        'repliesByParent' => $commentReplies,
                    ])
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    <!-- Table of Contents -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="h6 fw-bold mb-3">In This Article</h4>
                            <nav id="toc" class="small">
                                <!-- Populated by JS -->
                            </nav>
                        </div>
                    </div>

                    <!-- More from Author -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="h6 fw-bold mb-3">More from {{ $article->user->name }}</h4>
                            @php
                            $moreFromAuthor = App\Models\Article::where('user_id', $article->user_id)
                                ->where('id', '!=', $article->id)
                                ->published()
                                ->latest()
                                ->take(3)
                                ->get();
                            @endphp
                            @foreach($moreFromAuthor as $more)
                            <a href="{{ url('/blogs/' . $more->slug) }}" class="d-flex gap-3 mb-3 text-decoration-none text-dark">
                                <img src="{{ $more->image }}" alt="{{ $more->title }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <div class="fw-semibold small">{{ Str::limit($more->title, 40) }}</div>
                                    <x-time-ago :date="$more->published_at" />
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Posts -->
        @if($related->isNotEmpty())
        <div class="mt-5">
            <h3 class="h4 fw-bold mb-4">Related Articles</h3>
            <div class="row g-4">
                @foreach($related as $rel)
                <div class="col-md-6 col-lg-3">
                    <a href="{{ url('/blogs/' . $rel->slug) }}" class="text-decoration-none">
                        <div class="card h-100">
                            <img src="{{ $rel->image }}" alt="{{ $rel->title }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">{{ ucfirst($rel->category_slug) }}</span>
                                <h4 class="h6 fw-semibold text-dark">{{ Str::limit($rel->title, 50) }}</h4>
                                <small class="text-muted">{{ $rel->user->name }} &bull; {{ $rel->reading_time }}</small>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Copy link with toast
    const copyBtn = document.getElementById('copyLinkBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('Link Copied', 'Article URL copied to clipboard.', 'fa-solid fa-link text-success');
            });
        });
    }

    const token = window.csrfToken || '{{ csrf_token() }}';

    const postJson = (url) => fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
    }).then(r => r.json());

    const requireAuth = () => {
        if (window.isAuthenticated === false) {
            if (window.ToggleActions?.showLoginModal) {
                window.ToggleActions.showLoginModal();
            } else {
                window.location.href = window.loginUrl || '/login';
            }
            return false;
        }
        return true;
    };

    // Like button
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn) {
        likeBtn.addEventListener('click', () => {
            if (!requireAuth()) return;

            const icon = likeBtn.querySelector('i');
            const countEl = document.getElementById('likeCount');
            const wasLiked = likeBtn.getAttribute('aria-pressed') === 'true';

            likeBtn.setAttribute('aria-pressed', String(!wasLiked));
            likeBtn.classList.toggle('btn-danger', !wasLiked);
            likeBtn.classList.toggle('btn-outline-danger', wasLiked);
            icon.classList.toggle('fa-solid', !wasLiked);
            icon.classList.toggle('fa-regular', wasLiked);

            postJson('/articles/{{ $article->id }}/like').then((data) => {
                if (data.error) {
                    // Roll back
                    likeBtn.setAttribute('aria-pressed', String(wasLiked));
                    likeBtn.classList.toggle('btn-danger', wasLiked);
                    likeBtn.classList.toggle('btn-outline-danger', !wasLiked);
                    icon.classList.toggle('fa-solid', wasLiked);
                    icon.classList.toggle('fa-regular', !wasLiked);
                    showToast('Error', data.error, 'fa-solid fa-triangle-exclamation text-danger');
                    return;
                }
                countEl.textContent = data.likes;
                showToast(data.liked ? 'Added to favourites' : 'Removed from favourites', '', 'fa-solid fa-heart');
            }).catch(() => showToast('Error', 'Could not update like.', 'fa-solid fa-triangle-exclamation text-danger'));
        });
    }

    // Bookmark button
    const bookmarkBtn = document.getElementById('bookmarkBtn');
    if (bookmarkBtn) {
        bookmarkBtn.addEventListener('click', () => {
            if (!requireAuth()) return;

            const icon = bookmarkBtn.querySelector('i');
            const label = document.getElementById('bookmarkLabel');

            postJson('/articles/{{ $article->id }}/bookmark').then((data) => {
                if (data.error) {
                    showToast('Error', data.error, 'fa-solid fa-triangle-exclamation text-danger');
                    return;
                }
                bookmarkBtn.setAttribute('aria-pressed', String(!!data.bookmarked));
                bookmarkBtn.classList.toggle('btn-primary', !!data.bookmarked);
                bookmarkBtn.classList.toggle('btn-outline-primary', !data.bookmarked);
                icon.classList.toggle('fa-solid', !!data.bookmarked);
                icon.classList.toggle('fa-regular', !data.bookmarked);
                if (label) label.textContent = data.bookmarked ? 'Bookmarked' : 'Bookmark';
                showToast(data.bookmarked ? 'Saved to bookmarks' : 'Removed from bookmarks', '', 'fa-solid fa-bookmark');
            }).catch(() => showToast('Error', 'Could not update bookmark.', 'fa-solid fa-triangle-exclamation text-danger'));
        });
    }

    // Populate TOC
    const toc = document.getElementById('toc');
    if (toc) {
        document.querySelectorAll('.article-body h2, .article-body h3').forEach((h, i) => {
            h.id = 'section-' + i;
            const link = document.createElement('a');
            link.href = '#section-' + i;
            link.className = 'd-block py-1 text-decoration-none text-dark';
            link.textContent = h.textContent;
            toc.appendChild(link);
        });
    }
});
</script>
{{-- Threaded comments: posting, nested replies, likes, deletes --}}
<script src="{{ asset('assets/js/comments.js') }}" defer></script>
@endpush