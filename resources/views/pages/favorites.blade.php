@extends('layouts.app')

@section('title', 'My Favourites — BlogHub')
@section('meta_description', 'Everything you liked, saved and followed on BlogHub in one dashboard.')

@section('content')
<section class="py-5">
    <div class="container">

        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <span class="text-primary fw-semibold">Your Library</span>
                <h1 class="h2 fw-bold mb-1">My Favourites</h1>
                <p class="text-muted mb-0">
                    @if($total > 0)
                        {{ number_format($total) }} {{ $total === 1 ? 'item' : 'items' }} saved across posts, comments, bookmarks and authors.
                    @else
                        Nothing saved yet &mdash; favourite a post, like a comment or follow an author to build your library.
                    @endif
                </p>
            </div>
            <a href="{{ url('/blogs') }}" class="btn btn-bh btn-bh-primary">
                <i class="fa-solid fa-compass me-2"></i>Discover Articles
            </a>
        </div>

        <!-- Summary cards -->
        <div class="row g-3 mb-4">
            @php
                $summaryCards = [
                    ['posts', 'fa-solid fa-heart', 'Favourited Posts', $counts['posts'], 'red'],
                    ['comments', 'fa-solid fa-comment-dots', 'Liked Comments', $counts['comments'], 'blue'],
                    ['bookmarks', 'fa-solid fa-bookmark', 'Bookmarks', $counts['bookmarks'], 'green'],
                    ['following', 'fa-solid fa-user-group', 'Following', $counts['following'], 'orange'],
                ];
            @endphp
            @foreach($summaryCards as [$key, $icon, $label, $value, $color])
            <div class="col-6 col-lg-3">
                <a href="{{ route('favorites.index', ['tab' => $key]) }}"
                   class="card h-100 text-decoration-none fav-summary {{ $tab === $key ? 'is-active' : '' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="fav-summary__icon fav-summary__icon--{{ $color }}">
                            <i class="{{ $icon }}"></i>
                        </span>
                        <div>
                            <div class="h4 mb-0">{{ number_format($value) }}</div>
                            <small class="text-muted">{{ $label }}</small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Tabs -->
        <ul class="nav nav-pills mb-4 gap-2 flex-wrap">
            @foreach([
                ['posts', 'Favourited Posts', 'fa-solid fa-heart'],
                ['comments', 'Liked Comments', 'fa-solid fa-comment-dots'],
                ['bookmarks', 'Bookmarks', 'fa-solid fa-bookmark'],
                ['following', 'Following', 'fa-solid fa-user-group'],
            ] as [$key, $label, $icon])
            <li class="nav-item">
                <a class="nav-link {{ $tab === $key ? 'active' : 'bg-transparent border' }}"
                   href="{{ route('favorites.index', ['tab' => $key]) }}">
                    <i class="{{ $icon }} me-2"></i>{{ $label }}
                    <span class="badge rounded-pill ms-2 {{ $tab === $key ? 'text-bg-light' : 'text-bg-secondary' }}">{{ $counts[$key] }}</span>
                </a>
            </li>
            @endforeach
        </ul>

        {{-- ============================ POSTS ============================ --}}
        @if($tab === 'posts')
            @forelse($data['posts'] as $likedPost)
                @php
                    $article = $likedPost;
                    $likedAt = $likedPost->pivot?->created_at;
                @endphp
                <div class="card mb-3 fav-row" data-fav-row>
                    <div class="card-body d-flex flex-column flex-md-row gap-3">
                        <a href="{{ route('blogs.show', $article->slug) }}" class="flex-shrink-0">
                            <img src="{{ $article->image }}" alt="{{ $article->title }}"
                                 class="rounded fav-row__img" loading="lazy">
                        </a>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge bg-primary">{{ ucfirst($article->category_slug) }}</span>
                                <small class="text-muted">
                                    <i class="fa-regular fa-calendar me-1"></i>{{ $article->published_at?->format('M d, Y') }}
                                </small>
                                @if($likedAt)
                                <small class="text-muted">
                                    <i class="fa-solid fa-heart me-1 text-danger"></i><x-time-ago :date="$likedAt" prefix="Liked " />
                                </small>
                                @endif
                            </div>
                            <h3 class="h5 fw-bold mb-2">
                                <a href="{{ route('blogs.show', $article->slug) }}" class="text-decoration-none text-dark">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-muted small mb-3">{{ Str::limit($article->excerpt, 150) }}</p>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                @if($article->user?->profileUrl())
                                <a href="{{ $article->user->profileUrl() }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                                    @if($article->user->avatar)
                                    <img src="{{ $article->user->avatar }}" alt="" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                    @endif
                                    <small class="fw-semibold">{{ $article->user->name }}</small>
                                </a>
                                @endif
                                <small class="text-muted"><i class="fa-solid fa-eye me-1"></i>{{ number_format($article->views) }}</small>
                                <small class="text-muted"><i class="fa-solid fa-heart me-1"></i>{{ number_format($article->likes) }}</small>
                                <small class="text-muted"><i class="fa-regular fa-comment me-1"></i>{{ number_format($article->comment_count) }}</small>
                                <small class="text-muted">{{ $article->reading_time }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    data-fav-action="like" data-id="{{ $article->id }}"
                                    data-title="Remove from favourites?">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                            <a href="{{ route('blogs.show', $article->slug) }}" class="btn btn-sm btn-bh btn-bh-primary">Read</a>
                        </div>
                    </div>
                </div>
            @empty
                @include('pages.partials.favorites-empty', [
                    'icon' => 'fa-regular fa-heart',
                    'title' => 'No favourited posts yet',
                    'text' => 'Tap the heart on any article to add it here.',
                    'cta' => 'Browse Articles',
                    'ctaUrl' => url('/blogs'),
                ])
            @endforelse

            @include('pages.partials.favorites-pagination', ['paginator' => $data['posts'] ?? null])
        @endif

        {{-- =========================== COMMENTS =========================== --}}
        @if($tab === 'comments')
            @forelse($data['comments'] as $comment)
                @php $likedAt = $comment->pivot?->created_at; @endphp

                <div class="card mb-3 fav-row" data-fav-row>
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            @if($comment->user?->avatar)
                            <img src="{{ $comment->user->avatar }}" alt="" class="rounded-circle flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                  style="width: 40px; height: 40px; background: var(--color-primary); color: #fff;">
                                {{ strtoupper(substr($comment->user?->name ?? 'U', 0, 1)) }}
                            </span>
                            @endif

                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="fw-semibold">{{ $comment->user?->name ?? 'User' }}</span>
                                    @if($comment->parent)
                                    <small class="text-muted">
                                        <i class="fa-solid fa-reply me-1"></i>replied to {{ Str::limit($comment->parent->user?->name ?? 'a comment', 24) }}
                                    </small>
                                    @endif
                                    <x-time-ago :date="$comment->created_at" />
                                    @if($likedAt)
                                    <small class="text-muted"><i class="fa-solid fa-heart me-1 text-danger"></i><x-time-ago :date="$likedAt" prefix="Liked " /></small>
                                    @endif
                                </div>

                                <p class="mb-2" style="white-space: pre-line;">{{ Str::limit($comment->content, 400) }}</p>

                                @if($comment->article)
                                <a href="{{ route('blogs.show', $comment->article->slug) }}#comment-{{ $comment->id }}"
                                   class="small text-decoration-none">
                                    <i class="fa-solid fa-arrow-right-long me-1"></i>
                                    On “{{ Str::limit($comment->article->title, 60) }}”
                                </a>
                                @endif
                            </div>

                            <div class="flex-shrink-0 text-end">
                                <div class="fav-like-pill mb-2" title="Total likes on this comment">
                                    <i class="fa-solid fa-heart"></i> {{ number_format($comment->likes) }}
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-fav-action="comment-like" data-id="{{ $comment->id }}"
                                            data-title="Remove like from this comment?">
                                        <i class="fa-solid fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                @include('pages.partials.favorites-empty', [
                    'icon' => 'fa-regular fa-comment-dots',
                    'title' => 'No liked comments yet',
                    'text' => 'Like a comment in any discussion thread and it will show up here.',
                    'cta' => 'Read Latest Posts',
                    'ctaUrl' => url('/blogs'),
                ])
            @endforelse

            @include('pages.partials.favorites-pagination', ['paginator' => $data['comments'] ?? null])
        @endif

        {{-- =========================== BOOKMARKS ========================== --}}
        @if($tab === 'bookmarks')
            @forelse($data['bookmarks'] as $article)
                @php $savedAt = $article->pivot?->created_at; @endphp

                <div class="card mb-3 fav-row" data-fav-row>
                    <div class="card-body d-flex flex-column flex-md-row gap-3">
                        <a href="{{ route('blogs.show', $article->slug) }}" class="flex-shrink-0">
                            <img src="{{ $article->image }}" alt="{{ $article->title }}"
                                 class="rounded fav-row__img" loading="lazy">
                        </a>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge bg-primary">{{ ucfirst($article->category_slug) }}</span>
                                <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i>{{ $article->published_at?->format('M d, Y') }}</small>
                                @if($savedAt)
                                <small class="text-muted"><i class="fa-solid fa-bookmark me-1"></i><x-time-ago :date="$savedAt" prefix="Saved " /></small>
                                @endif
                            </div>
                            <h3 class="h5 fw-bold mb-2">
                                <a href="{{ route('blogs.show', $article->slug) }}" class="text-decoration-none text-dark">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-muted small mb-3">{{ Str::limit($article->excerpt, 150) }}</p>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                @if($article->user?->profileUrl())
                                <a href="{{ $article->user->profileUrl() }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                                    @if($article->user->avatar)
                                    <img src="{{ $article->user->avatar }}" alt="" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                    @endif
                                    <small class="fw-semibold">{{ $article->user->name }}</small>
                                </a>
                                @endif
                                <small class="text-muted">{{ $article->reading_time }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2 flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-fav-action="bookmark" data-id="{{ $article->id }}"
                                    data-title="Remove from bookmarks?">
                                <i class="fa-solid fa-bookmark"></i>
                            </button>
                            <a href="{{ route('blogs.show', $article->slug) }}" class="btn btn-sm btn-bh btn-bh-primary">Read</a>
                        </div>
                    </div>
                </div>
            @empty
                @include('pages.partials.favorites-empty', [
                    'icon' => 'fa-regular fa-bookmark',
                    'title' => 'No bookmarks yet',
                    'text' => 'Save articles for later and they will be waiting here.',
                    'cta' => 'Browse Articles',
                    'ctaUrl' => url('/blogs'),
                ])
            @endforelse

            @include('pages.partials.favorites-pagination', ['paginator' => $data['bookmarks'] ?? null])
        @endif

        {{-- ========================== FOLLOWING =========================== --}}
        @if($tab === 'following')
            <div class="row g-3">
                @forelse($data['authors'] as $author)
                    @php $followedAt = $author->pivot?->created_at; @endphp
                    <div class="col-md-6 col-lg-4" data-fav-row>
                        <div class="card h-100">
                            <div class="card-body text-center">
                                @if($author->avatar)
                                <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="rounded-circle mb-3" style="width: 72px; height: 72px; object-fit: cover;">
                                @else
                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 fw-bold"
                                      style="width: 72px; height: 72px; background: var(--color-primary); color: #fff; font-size: 1.75rem;">
                                    {{ strtoupper(substr($author->name, 0, 1)) }}
                                </span>
                                @endif

                                <h3 class="h6 fw-bold mb-1">
                                    @if($author->profileUrl())
                                    <a href="{{ $author->profileUrl() }}" class="text-decoration-none text-dark">{{ $author->name }}</a>
                                    @else
                                    {{ $author->name }}
                                    @endif
                                </h3>
                                <p class="text-muted small mb-3">{{ $author->tagline ?: Str::limit($author->bio, 70) }}</p>

                                <div class="d-flex justify-content-center gap-3 mb-3">
                                    <div>
                                        <div class="fw-bold">{{ number_format($author->articles_count) }}</div>
                                        <small class="text-muted">Posts</small>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ number_format($author->followers_count) }}</div>
                                        <small class="text-muted">Followers</small>
                                    </div>
                                </div>

                                @if($followedAt)
                                <small class="text-muted d-block mb-2">Following since {{ $followedAt->format('M Y') }}</small>
                                @endif

                                <button type="button" class="btn btn-sm btn-outline-primary w-100"
                                        data-fav-action="follow" data-id="{{ $author->id }}"
                                        data-title="Unfollow this author?">
                                    <i class="fa-solid fa-user-minus me-2"></i>Unfollow
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        @include('pages.partials.favorites-empty', [
                            'icon' => 'fa-regular fa-user-group',
                            'title' => 'Not following anyone yet',
                            'text' => 'Follow authors you enjoy and their new posts will show up here.',
                            'cta' => 'Browse Authors',
                            'ctaUrl' => url('/authors'),
                        ])
                    </div>
                @endforelse
            </div>

            @include('pages.partials.favorites-pagination', ['paginator' => $data['authors'] ?? null])
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
    .fav-summary {
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .fav-summary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
    }
    .fav-summary.is-active {
        border-color: var(--color-primary);
        box-shadow: inset 0 0 0 1px var(--color-primary);
    }
    .fav-summary__icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.05rem;
    }
    .fav-summary__icon--red    { background: rgba(200, 70, 31, 0.12); color: #C8461F; }
    .fav-summary__icon--blue   { background: rgba(31, 42, 68, 0.10); color: #1F2A44; }
    .fav-summary__icon--green  { background: rgba(25, 135, 84, 0.12); color: #198754; }
    .fav-summary__icon--orange { background: rgba(255, 193, 7, 0.18); color: #b58105; }

    .fav-row__img {
        width: 190px;
        height: 120px;
        object-fit: cover;
    }
    @media (max-width: 575.98px) {
        .fav-row__img {
            width: 100%;
            height: 170px;
        }
    }

    .fav-like-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        background: var(--color-accent-subtle, #FDF1ED);
        color: var(--color-accent, #C8461F);
        font-size: 0.8125rem;
        font-weight: 700;
    }

    .nav-pills .nav-link {
        font-weight: 600;
        color: var(--color-text-muted);
    }
    .nav-pills .nav-link.active {
        background-color: var(--color-primary);
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = window.csrfToken || '{{ csrf_token() }}';

    const endpoints = {
        like: (id) => `/articles/${id}/like`,
        bookmark: (id) => `/articles/${id}/bookmark`,
        follow: (id) => `/authors/${id}/follow`,
        'comment-like': (id) => `/comments/${id}/like`
    };

    // Decrement the matching summary card + tab badge when a row is removed.
    function decrementCounter(tab) {
        document.querySelectorAll(`[href*="tab=${tab}"] .badge`).forEach((badge) => {
            const next = Math.max(0, (parseInt(badge.textContent.replace(/[^\d]/g, ''), 10) || 0) - 1);
            badge.textContent = next;
        });

        const card = document.querySelector(`.fav-summary[href*="tab=${tab}"] .h4`);
        if (card) {
            const next = Math.max(0, (parseInt(card.textContent.replace(/[^\d]/g, ''), 10) || 0) - 1);
            card.textContent = next.toLocaleString();
        }
    }

    document.querySelectorAll('[data-fav-action]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const action = btn.dataset.favAction;
            const id = btn.dataset.id;
            const row = btn.closest('[data-fav-row]');
            const confirmText = btn.dataset.title;

            if (confirmText && !window.confirm(confirmText)) return;

            btn.disabled = true;

            try {
                const response = await fetch(endpoints[action](id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                const data = await response.json();

                if (!response.ok || data.error) {
                    showToast('Error', data.error || 'Could not update.', 'fa-solid fa-triangle-exclamation text-danger');
                    btn.disabled = false;
                    return;
                }

                // Only remove the row when the item really left the list.
                const removed = action === 'comment-like'
                    ? !data.liked
                    : action === 'like' ? !data.liked
                    : action === 'bookmark' ? !data.bookmarked
                    : !data.following;

                if (removed) {
                    row.style.transition = 'opacity .2s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        decrementCounter(action === 'comment-like' ? 'comments' : action === 'like' ? 'posts' : action === 'bookmark' ? 'bookmarks' : 'following');
                        showToast('Removed', 'Item removed from your library.', 'fa-solid fa-check');
                    }, 200);
                } else {
                    showToast('Restored', 'Item added back to your library.', 'fa-solid fa-check');
                    btn.disabled = false;
                }
            } catch (error) {
                btn.disabled = false;
                showToast('Error', 'Something went wrong. Please try again.', 'fa-solid fa-triangle-exclamation text-danger');
            }
        });
    });
});
</script>
@endpush