@props([
    'comment',
    'repliesByParent' => [],
    'depth' => 0,
])

@php
    $childReplies = $repliesByParent[$comment->id] ?? [];
    $visibleReplies = array_slice($childReplies, 0, 3);
    $hiddenReplies = array_slice($childReplies, 3);
    $isLiked = $comment->isLikedBy(auth()->user());
    $author = $comment->user;
    $isPostOwner = auth()->check() && auth()->id() === $comment->article?->user_id;
    $canDelete = auth()->check() && (auth()->id() === $comment->user_id || $isPostOwner || auth()->user()->hasRole('admin'));
@endphp

<div class="bh-comment" id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}" data-depth="{{ $depth }}">

    <div class="d-flex gap-3">
        {{-- Avatar --}}
        @if($author?->avatar)
        <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="rounded-circle flex-shrink-0" style="width: {{ $depth > 2 ? 32 : 40 }}px; height: {{ $depth > 2 ? 32 : 40 }}px; object-fit: cover;">
        @else
        <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
              style="width: {{ $depth > 2 ? 32 : 40 }}px; height: {{ $depth > 2 ? 32 : 40 }}px; background: var(--color-primary); color: #fff; font-size: {{ $depth > 2 ? 0.75 : 0.9 }}rem;">
            {{ strtoupper(substr($author?->name ?? 'U', 0, 1)) }}
        </span>
        @endif

        <div class="flex-grow-1" style="min-width: 0;">
            {{-- Header --}}
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if($author?->profileUrl())
                <a href="{{ $author->profileUrl() }}" class="fw-semibold text-decoration-none">{{ $author->name }}</a>
                @else
                <span class="fw-semibold">{{ $author?->name ?? 'User' }}</span>
                @endif

                @if($isPostOwner)
                <span class="bh-badge bh-badge-primary" style="font-size: 0.65rem;">Author</span>
                @elseif($author?->hasRole('admin'))
                <span class="bh-badge bh-badge-accent" style="font-size: 0.65rem;">Admin</span>
                @endif

                <x-time-ago :date="$comment->created_at" class="text-muted" />
            </div>

            {{-- Body --}}
            <p class="mb-2 mt-1" style="white-space: pre-line;">{{ $comment->content }}</p>

            {{-- Actions --}}
            <div class="d-flex flex-wrap align-items-center gap-2 bh-comment__actions">
                {{-- Like --}}
                <button type="button"
                        class="btn btn-sm btn-link p-0 bh-comment-like {{ $isLiked ? 'is-liked' : '' }}"
                        data-action="comment-like"
                        data-comment-id="{{ $comment->id }}"
                        aria-pressed="{{ $isLiked ? 'true' : 'false' }}">
                    <i class="{{ $isLiked ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                    <span class="bh-comment-like__count" data-like-count>{{ $comment->likes }}</span>
                </button>

                {{-- Reply --}}
                @auth
                <button type="button" class="btn btn-sm btn-link p-0 bh-comment-reply-btn"
                        data-action="comment-reply" data-comment-id="{{ $comment->id }}">
                    <i class="fa-solid fa-reply me-1"></i>Reply
                </button>
                @endauth

                {{-- Delete --}}
                @if($canDelete)
                <button type="button" class="btn btn-sm btn-link p-0 text-danger bh-comment-delete"
                        data-action="comment-delete" data-comment-id="{{ $comment->id }}">
                    <i class="fa-solid fa-trash me-1"></i>Delete
                </button>
                @endif
            </div>

            {{-- Inline reply form --}}
            @auth
            <form class="bh-comment-reply-form d-none mt-3" data-reply-form="{{ $comment->id }}" hidden>
                <textarea class="form-control form-control-sm mb-2" rows="2" maxlength="1000"
                          placeholder="Write a reply&hellip;"
                          aria-label="Reply to {{ $author?->name ?? 'comment' }}"></textarea>
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-sm btn-bh btn-bh-primary">Post Reply</button>
                    <button type="button" class="btn btn-sm btn-link p-0 text-muted bh-comment-reply-cancel" data-comment-id="{{ $comment->id }}">Cancel</button>
                    <small class="text-muted ms-auto reply-hint" style="font-size: 0.75rem;">0 / 1000</small>
                </div>
            </form>
            @endauth
        </div>
    </div>

    {{-- Nested replies --}}
    @if(!empty($childReplies))
    <div class="bh-comment__replies" style="--depth: {{ $depth }};">
        @foreach($visibleReplies as $reply)
            <x-comment-item :comment="$reply" :replies-by-parent="$repliesByParent" :depth="$depth + 1" />
        @endforeach

        @if(!empty($hiddenReplies))
        <div class="d-none" data-hidden-replies>
            @foreach($hiddenReplies as $reply)
                <x-comment-item :comment="$reply" :replies-by-parent="$repliesByParent" :depth="$depth + 1" />
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-link p-0 bh-comment__more" data-action="show-more-replies">
            <i class="fa-solid fa-chevron-down me-1"></i>Show {{ count($hiddenReplies) }} more {{ count($hiddenReplies) === 1 ? 'reply' : 'replies' }}
        </button>
        @endif
    </div>
    @endif
</div>