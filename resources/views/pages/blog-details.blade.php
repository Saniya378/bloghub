@extends('layouts.app')

@section('title', $article->title . ' — BlogHub')
@section('meta_description', $article->excerpt)

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
                <a href="{{ route('authors.show', $article->user->username) }}" class="d-flex align-items-center text-decoration-none text-dark">
                    <img src="{{ $article->user->avatar }}" alt="{{ $article->user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                    <div>
                        <div class="fw-semibold">{{ $article->user->name }}</div>
                        <small class="text-muted">{{ $article->user->tagline }}</small>
                    </div>
                </a>
                <span><i class="fa-regular fa-calendar me-1"></i>{{ $article->published_at->format('M d, Y') }}</span>
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
                    <button class="btn btn-outline-danger" id="likeBtn" data-article-id="{{ $article->id }}">
                        <i class="fa-regular fa-heart me-1"></i> <span id="likeCount">{{ $article->likes }}</span>
                    </button>
                    <button class="btn btn-outline-primary" id="bookmarkBtn" data-article-id="{{ $article->id }}">
                        <i class="fa-regular fa-bookmark me-1"></i> Bookmark
                    </button>
                </div>

                <!-- Author Box -->
                <div class="card my-4">
                    <div class="card-body d-flex gap-4">
                        <img src="{{ $article->user->avatar }}" alt="{{ $article->user->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <div class="text-muted small">Written by</div>
                            <h3 class="h5 fw-bold mb-1">
                                <a href="{{ route('authors.show', $article->user->username) }}" class="text-decoration-none text-dark">{{ $article->user->name }}</a>
                            </h3>
                            <p class="text-muted mb-2">{{ $article->user->bio }}</p>
                            <a href="{{ route('authors.show', $article->user->username) }}" class="btn btn-sm btn-outline-primary">View Profile</a>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="my-5">
                    <h3 class="h4 fw-bold mb-4">Comments <span id="commentCount">({{ $article->comments->where('status', 'approved')->count() }})</span></h3>

                    @auth
                    <form id="commentForm" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="commentText" class="form-label">Add a comment</label>
                            <textarea class="form-control" id="commentText" name="content" rows="3" placeholder="Share your thoughts..." required maxlength="500"></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted" id="charCounter">0 / 500</small>
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
                        <a href="{{ url('/login') }}">Sign in</a> to leave a comment.
                    </div>
                    @endauth

                    <div id="commentsList">
                        @foreach($article->comments->where('status', 'approved')->where('parent_id', null) as $comment)
                        <div class="d-flex gap-3 mb-4" id="comment-{{ $comment->id }}">
                            <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold">{{ $comment->user->name }}</span>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-2">{{ $comment->content }}</p>
                                <div class="d-flex gap-3">
                                    @auth
                                    <button class="btn btn-sm btn-link p-0 text-primary reply-btn" data-comment-id="{{ $comment->id }}">Reply</button>
                                    @if(auth()->id() === $comment->user_id || auth()->user()->role === 'admin')
                                    <button class="btn btn-sm btn-link p-0 text-danger delete-comment-btn" data-comment-id="{{ $comment->id }}">Delete</button>
                                    @endif
                                    @endauth
                                </div>
                                <!-- Reply form (hidden by default) -->
                                <div class="reply-form mt-3 d-none" id="reply-form-{{ $comment->id }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control reply-input" placeholder="Write a reply..." maxlength="500">
                                        <button class="btn btn-primary submit-reply" data-comment-id="{{ $comment->id }}">Reply</button>
                                    </div>
                                </div>
                                <!-- Replies -->
                                @foreach($comment->replies->where('status', 'approved') as $reply)
                                <div class="d-flex gap-3 mt-3 ms-4">
                                    <img src="{{ $reply->user->avatar }}" alt="{{ $reply->user->name }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold small">{{ $reply->user->name }}</span>
                                            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 small">{{ $reply->content }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Load More -->
                    <div class="text-center mt-4" id="loadMoreContainer">
                        <button class="btn btn-outline-primary" id="loadMoreBtn">Load More Comments</button>
                    </div>
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
                                    <small class="text-muted">{{ $more->published_at->diffForHumans() }}</small>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;
    const perPage = 5;

    // Copy link with toast
    const copyBtn = document.getElementById('copyLinkBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('Link Copied', 'Article URL copied to clipboard.', 'fa-solid fa-link text-success');
            });
        });
    }

    // Like button
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn) {
        likeBtn.addEventListener('click', () => {
            fetch('/articles/{{ $article->id }}/like', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('likeCount').textContent = data.likes;
                    showToast(data.liked ? 'Liked' : 'Unliked', '', 'fa-solid fa-heart');
                });
        });
    }

    // Bookmark button
    const bookmarkBtn = document.getElementById('bookmarkBtn');
    if (bookmarkBtn) {
        bookmarkBtn.addEventListener('click', () => {
            fetch('/articles/{{ $article->id }}/bookmark', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(r => r.json())
                .then(data => showToast(data.bookmarked ? 'Bookmarked' : 'Removed', '', 'fa-solid fa-bookmark'));
        });
    }

    // Comment character counter
    const commentText = document.getElementById('commentText');
    const charCounter = document.getElementById('charCounter');
    if (commentText && charCounter) {
        commentText.addEventListener('input', () => {
            charCounter.textContent = `${commentText.value.length} / 500`;
            if (commentText.value.length > 500) {
                charCounter.classList.add('text-danger');
            } else {
                charCounter.classList.remove('text-danger');
            }
        });
    }

    // Comment form submission
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('commentSubmitBtn');
            const spinner = document.getElementById('commentSpinner');
            const btnText = document.getElementById('commentBtnText');
            const rateLimitInfo = document.getElementById('rateLimitInfo');

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.textContent = 'Posting...';

            try {
                const response = await fetch('/comments', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        article_id: {{ $article->id }},
                        content: commentText.value,
                        parent_id: null
                    })
                });
                const data = await response.json();

                if (data.success) {
                    const isAutoApproved = data.comment && data.comment.status === 'approved';
                    showToast('Comment Posted', isAutoApproved ? 'Your comment is now live.' : 'Your comment is awaiting moderation.', 'fa-solid fa-check');
                    commentForm.reset();
                    charCounter.textContent = '0 / 500';
                    // Reload comments to show new one
                    loadComments(1, true);
                } else if (response.status === 429) {
                    rateLimitInfo.textContent = 'Too many comments. Please wait a minute.';
                    showToast('Rate Limited', 'Maximum 5 comments per minute.', 'fa-solid fa-exclamation-circle text-danger');
                } else {
                    showToast('Error', data.message || 'Failed to post comment.', 'fa-solid fa-exclamation-circle text-danger');
                }
            } catch (error) {
                showToast('Error', 'Something went wrong. Please try again.', 'fa-solid fa-exclamation-circle text-danger');
            } finally {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.textContent = 'Post Comment';
            }
        });
    }

    // Load more comments
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            currentPage++;
            loadComments(currentPage, false);
        });
    }

    function loadComments(page, replace = false) {
        fetch(`/api/comments/{{ $article->id }}?page=${page}&per_page=${perPage}`)
            .then(r => r.json())
            .then(data => {
                const commentsList = document.getElementById('commentsList');
                if (replace) {
                    commentsList.innerHTML = data.html;
                } else {
                    commentsList.insertAdjacentHTML('beforeend', data.html);
                }
                document.getElementById('commentCount').textContent = `(${data.total})`;
                if (!data.has_more) {
                    document.getElementById('loadMoreContainer').classList.add('d-none');
                }
            });
    }

    // Reply buttons
    document.querySelectorAll('.reply-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const commentId = btn.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            replyForm.classList.toggle('d-none');
        });
    });

    // Submit reply
    document.querySelectorAll('.submit-reply').forEach(btn => {
        btn.addEventListener('click', async () => {
            const commentId = btn.dataset.commentId;
            const input = btn.closest('.reply-form').querySelector('.reply-input');
            const content = input.value.trim();
            if (!content) return;

            try {
                const response = await fetch('/comments', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        article_id: {{ $article->id }},
                        content: content,
                        parent_id: commentId
                    })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Reply Posted', data.comment.status === 'approved' ? 'Your reply is now live.' : 'Your reply is awaiting moderation.', 'fa-solid fa-check');
                    input.value = '';
                    btn.closest('.reply-form').classList.add('d-none');
                    loadComments(1, true);
                }
            } catch (error) {
                showToast('Error', 'Failed to post reply.', 'fa-solid fa-exclamation-circle text-danger');
            }
        });
    });

    // Delete comment
    document.querySelectorAll('.delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Are you sure you want to delete this comment?')) return;
            const commentId = btn.dataset.commentId;
            try {
                const response = await fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById(`comment-${commentId}`)?.remove();
                    showToast('Deleted', 'Comment has been removed.', 'fa-solid fa-trash');
                }
            } catch (error) {
                showToast('Error', 'Failed to delete comment.', 'fa-solid fa-exclamation-circle text-danger');
            }
        });
    });

    // Populate TOC
    const toc = document.getElementById('toc');
    if (toc) {
        const headings = document.querySelectorAll('.article-body h2, .article-body h3');
        headings.forEach((h, i) => {
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
@endpush
