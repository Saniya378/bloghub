{{--
    Comment thread fragment for an article.

    Rendered inline on the blog detail page and returned by
    CommentController::thread() to refresh the thread without a page reload.
--}}
<div id="commentsList">
    @forelse($comments as $comment)
        <x-comment-item :comment="$comment" :replies-by-parent="$repliesByParent" :depth="0" />
    @empty
        <div class="text-center py-5 text-muted" id="noComments">
            <i class="fa-regular fa-comments fa-2x d-block mb-3"></i>
            <p class="mb-1 fw-semibold">No comments yet</p>
            <p class="mb-0 small">Be the first to share your thoughts.</p>
        </div>
    @endforelse
</div>