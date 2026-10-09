/**
 * BlogHub — Threaded Comments Module
 *
 * Handles posting top level comments, nested replies (unlimited depth),
 * like/unlike on any comment in the thread and deleting a comment subtree.
 *
 * Markup contract (see resources/views/components/comment-item.blade.php):
 *   #commentsSection[data-article-id][data-authenticated]
 *   #commentsList                      thread container
 *   [data-action="comment-like"]       like toggle button
 *   [data-action="comment-reply"]      opens the inline reply form
 *   [data-action="comment-delete"]     deletes comment + replies
 *   [data-action="show-more-replies"]  reveals collapsed replies
 */

const CommentActions = (function () {
    'use strict';

    const MAX_LENGTH = 1000;

    let section = null;
    let articleId = null;
    let isAuthenticated = false;

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    function csrfToken() {
        return window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function toast(title, message, icon) {
        if (typeof window.showToast === 'function') {
            window.showToast(title, message, icon || 'fa-solid fa-circle-info');
        }
    }

    function promptLogin() {
        if (typeof window.ToggleActions?.showLoginModal === 'function') {
            window.ToggleActions.showLoginModal();
        } else {
            window.location.href = window.loginUrl || '/login';
        }
    }

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(body || {})
        }).then(async (response) => {
            const data = await response.json().catch(() => ({}));
            return { ok: response.ok, status: response.status, data };
        });
    }

    function commentCount() {
        return document.getElementById('commentCount');
    }

    /* ------------------------------------------------------------------ */
    /* Thread refresh                                                       */
    /* ------------------------------------------------------------------ */

    function reloadThread() {
        const list = document.getElementById('commentsList');
        if (!list) return;

        fetch(`/comments/${articleId}/thread`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then((r) => r.json())
            .then((data) => {
                if (typeof data.html !== 'string') return;

                list.innerHTML = data.html;
                collapseDeepReplies(list);

                const counter = commentCount();
                if (counter) {
                    counter.textContent = `(${data.total})`;
                }
            })
            .catch(() => toast('Error', 'Could not refresh comments.', 'fa-solid fa-triangle-exclamation text-danger'));
    }

    /**
     * Deep threads get very long very fast, so anything past the second level
     * starts collapsed behind a "show N more replies" toggle.
     */
    function collapseDeepReplies(root) {
        root.querySelectorAll('.bh-comment__replies').forEach((container) => {
            const nodes = Array.from(container.children).filter((child) => child.classList.contains('bh-comment'));

            if (nodes.length > 3) {
                const hidden = document.createElement('div');
                hidden.className = 'd-none';
                hidden.setAttribute('data-hidden-replies', '');

                nodes.slice(3).forEach((node) => hidden.appendChild(node));
                container.appendChild(hidden);
            }

            // Cap how deep the visual indent grows.
            const depth = parseInt(container.style.getPropertyValue('--depth') || '0', 10);
            container.classList.toggle('bh-comment__replies--deep', depth >= 2);
        });
    }

    /* ------------------------------------------------------------------ */
    /* Like / unlike                                                        */
    /* ------------------------------------------------------------------ */

    async function toggleLike(button) {
        if (!isAuthenticated) {
            promptLogin();
            return;
        }

        const id = button.dataset.commentId;
        const icon = button.querySelector('i');
        const countEl = button.querySelector('[data-like-count]');
        const wasLiked = button.getAttribute('aria-pressed') === 'true';

        // Optimistic update
        button.setAttribute('aria-pressed', String(!wasLiked));
        button.classList.toggle('is-liked', !wasLiked);
        icon?.classList.toggle('fa-solid', !wasLiked);
        icon?.classList.toggle('fa-regular', wasLiked);
        if (countEl) {
            countEl.textContent = String(Math.max(0, (parseInt(countEl.textContent, 10) || 0) + (wasLiked ? -1 : 1)));
        }

        const { ok, data } = await post(`/comments/${id}/like`);

        if (!ok) {
            // Roll back
            button.setAttribute('aria-pressed', String(wasLiked));
            button.classList.toggle('is-liked', wasLiked);
            icon?.classList.toggle('fa-solid', wasLiked);
            icon?.classList.toggle('fa-regular', !wasLiked);
            if (countEl) {
                countEl.textContent = String(Math.max(0, (parseInt(countEl.textContent, 10) || 0) + (wasLiked ? 1 : -1)));
            }
            toast('Error', data.error || 'Could not update like.', 'fa-solid fa-triangle-exclamation text-danger');
            return;
        }

        // Trust the server value
        if (countEl) countEl.textContent = data.likes;
        button.setAttribute('aria-pressed', String(!!data.liked));
        button.classList.toggle('is-liked', !!data.liked);
        icon?.classList.toggle('fa-solid', !!data.liked);
        icon?.classList.toggle('fa-regular', !data.liked);
    }

    /* ------------------------------------------------------------------ */
    /* Reply forms                                                          */
    /* ------------------------------------------------------------------ */

    function openReplyForm(commentId, focus = true) {
        closeReplyForms();
        const form = document.querySelector(`[data-reply-form="${commentId}"]`);
        if (!form) return;

        form.classList.remove('d-none');
        form.removeAttribute('hidden');

        if (focus) {
            form.querySelector('textarea')?.focus();
        }
    }

    function closeReplyForms() {
        document.querySelectorAll('[data-reply-form]').forEach((form) => {
            form.classList.add('d-none');
            form.setAttribute('hidden', 'hidden');
        });
        document.querySelectorAll('[data-action="comment-reply"].is-open').forEach((btn) => {
            btn.classList.remove('is-open');
        });
    }

    function updateCounter(field) {
        const form = field.closest('[data-reply-form]');
        const hint = form?.querySelector('.reply-hint');
        if (!hint) return;

        hint.textContent = `${field.value.length} / ${MAX_LENGTH}`;
        hint.classList.toggle('text-danger', field.value.length > MAX_LENGTH);
    }

    async function submitReply(form) {
        const commentId = form.dataset.replyForm;
        const textarea = form.querySelector('textarea');
        const submitBtn = form.querySelector('button[type="submit"]');
        const content = textarea.value.trim();

        if (!content) {
            textarea.focus();
            return;
        }

        const originalLabel = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Posting…';

        const { ok, status, data } = await post('/comments', {
            article_id: articleId,
            parent_id: commentId,
            content
        });

        submitBtn.disabled = false;
        submitBtn.textContent = originalLabel;

        if (ok && data.success) {
            textarea.value = '';
            updateCounter(textarea);
            closeReplyForms();
            toast('Reply Posted', 'Your reply is awaiting moderation.', 'fa-solid fa-check');
            reloadThread();
            return;
        }

        if (status === 429) {
            toast('Rate Limited', 'You are commenting too fast. Try again in a minute.', 'fa-solid fa-triangle-exclamation text-danger');
            return;
        }

        const message = data.error || (data.errors?.content ? Object.values(data.errors.content)[0] : null) || 'Failed to post reply.';
        toast('Error', message, 'fa-solid fa-triangle-exclamation text-danger');
    }

    /* ------------------------------------------------------------------ */
    /* Delete                                                               */
    /* ------------------------------------------------------------------ */

    async function destroy(button) {
        const id = button.dataset.commentId;
        const node = document.getElementById(`comment-${id}`);
        const replyCount = node ? node.querySelectorAll('.bh-comment').length : 0;

        const warning = replyCount > 0
            ? `This will also remove ${replyCount} ${replyCount === 1 ? 'reply' : 'replies'}. Continue?`
            : 'Are you sure you want to delete this comment?';

        if (!window.confirm(warning)) return;

        const previousHtml = node?.outerHTML;

        fetch(`/comments/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(async (r) => ({ ok: r.ok, data: await r.json().catch(() => ({})) }))
            .then(({ ok, data }) => {
                if (!ok) {
                    toast('Error', data.error || 'Could not delete comment.', 'fa-solid fa-triangle-exclamation text-danger');
                    return;
                }

                node?.closest('.bh-comment')?.remove();
                closeReplyForms();

                const counter = commentCount();
                if (counter && data.removed) {
                    const current = parseInt(counter.textContent.replace(/[^\d]/g, ''), 10) || 0;
                    counter.textContent = `(${Math.max(0, current - data.removed)})`;
                }

                toast('Deleted', data.removed > 1 ? `Comment and ${data.removed - 1} replies removed.` : 'Comment removed.', 'fa-solid fa-trash');
            })
            .catch(() => {
                if (previousHtml && node) {
                    toast('Error', 'Could not delete comment.', 'fa-solid fa-triangle-exclamation text-danger');
                }
            });
    }

    /* ------------------------------------------------------------------ */
    /* Top level comment form                                               */
    /* ------------------------------------------------------------------ */

    async function submitComment(form) {
        const textarea = form.querySelector('#commentText');
        const submitBtn = document.getElementById('commentSubmitBtn');
        const spinner = document.getElementById('commentSpinner');
        const btnText = document.getElementById('commentBtnText');
        const rateLimitInfo = document.getElementById('rateLimitInfo');
        const charCounter = document.getElementById('charCounter');
        const content = textarea.value.trim();

        if (!content) {
            textarea.focus();
            return;
        }

        submitBtn.disabled = true;
        spinner?.classList.remove('d-none');
        if (btnText) btnText.textContent = 'Posting…';

        const { ok, status, data } = await post('/comments', {
            article_id: articleId,
            parent_id: null,
            content
        });

        submitBtn.disabled = false;
        spinner?.classList.add('d-none');
        if (btnText) btnText.textContent = 'Post Comment';

        if (ok && data.success) {
            form.reset();
            if (charCounter) charCounter.textContent = `0 / ${MAX_LENGTH}`;
            toast('Comment Posted', 'Your comment is awaiting moderation.', 'fa-solid fa-check');
            reloadThread();
            return;
        }

        if (status === 429) {
            if (rateLimitInfo) rateLimitInfo.textContent = 'Too many comments. Please wait a minute.';
            toast('Rate Limited', 'Maximum 10 comments per minute.', 'fa-solid fa-triangle-exclamation text-danger');
            return;
        }

        const message = data.error || (data.errors?.content ? Object.values(data.errors.content)[0] : null) || 'Failed to post comment.';
        toast('Error', message, 'fa-solid fa-triangle-exclamation text-danger');
    }

    /* ------------------------------------------------------------------ */
    /* Deep links                                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Deep links (e.g. /blogs/slug#comment-71) can point at a reply that is
     * currently hidden behind a "show more" toggle, which would leave the
     * reader on an empty anchor. Expand everything up to the target first.
     */
    function revealHashTarget(root) {
        const hash = window.location.hash;

        if (!hash || hash.length < 2) return;

        const target = root.querySelector(hash);

        if (!target) return;

        let node = target;

        while (node && node !== root) {
            if (node.classList?.contains('d-none') && node.hasAttribute('data-hidden-replies')) {
                node.classList.remove('d-none');
            }
            node = node.parentElement;
        }

        // Drop the now redundant "show more" toggles above the target.
        target.querySelectorAll('[data-action="show-more-replies"]').forEach((btn) => btn.remove());
        let ancestor = target.closest('.bh-comment')?.previousElementSibling;
        while (ancestor) {
            if (ancestor.matches?.('[data-action="show-more-replies"]')) {
                ancestor.remove();
            }
            ancestor = ancestor.previousElementSibling;
        }

        target.classList.add('bh-comment--highlighted');

        // The browser's own anchor scroll runs before this deferred script, so
        // it can land on a hidden node. Re-scroll once the target is visible.
        requestAnimationFrame(() => {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        setTimeout(() => target.classList.remove('bh-comment--highlighted'), 2500);
    }

    /* ------------------------------------------------------------------ */
    /* Wiring                                                               */
    /* ------------------------------------------------------------------ */

    function init() {
        section = document.getElementById('commentsSection');
        if (!section) return;

        articleId = section.dataset.articleId;
        isAuthenticated = section.dataset.authenticated === 'true';

        collapseDeepReplies(section);
        revealHashTarget(section);

        // Top level comment form
        const commentForm = document.getElementById('commentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitComment(commentForm);
            });

            const textarea = commentForm.querySelector('#commentText');
            const counter = document.getElementById('charCounter');
            textarea?.addEventListener('input', () => {
                if (!counter) return;
                counter.textContent = `${textarea.value.length} / ${MAX_LENGTH}`;
                counter.classList.toggle('text-danger', textarea.value.length > MAX_LENGTH);
            });
        }

        // Delegated handling for the whole thread (works after reloadThread())
        section.addEventListener('click', (event) => {
            const likeBtn = event.target.closest('[data-action="comment-like"]');
            if (likeBtn) {
                toggleLike(likeBtn);
                return;
            }

            const replyBtn = event.target.closest('[data-action="comment-reply"]');
            if (replyBtn) {
                if (!isAuthenticated) {
                    promptLogin();
                    return;
                }
                const id = replyBtn.dataset.commentId;
                const alreadyOpen = replyBtn.classList.contains('is-open');
                closeReplyForms();
                if (!alreadyOpen) {
                    replyBtn.classList.add('is-open');
                    openReplyForm(id);
                }
                return;
            }

            const cancelBtn = event.target.closest('.bh-comment-reply-cancel');
            if (cancelBtn) {
                closeReplyForms();
                return;
            }

            const deleteBtn = event.target.closest('[data-action="comment-delete"]');
            if (deleteBtn) {
                destroy(deleteBtn);
                return;
            }

            const moreBtn = event.target.closest('[data-action="show-more-replies"]');
            if (moreBtn) {
                moreBtn.previousElementSibling?.classList.remove('d-none');
                moreBtn.remove();
            }
        });

        // Reply form submit + live counter (delegated)
        section.addEventListener('submit', (event) => {
            const form = event.target.closest('[data-reply-form]');
            if (!form) return;

            event.preventDefault();
            submitReply(form);
        });

        section.addEventListener('input', (event) => {
            if (event.target.matches('[data-reply-form] textarea')) {
                updateCounter(event.target);
            }
        });
    }

    return { init, reloadThread };
})();

document.addEventListener('DOMContentLoaded', () => {
    CommentActions.init();
});

window.CommentActions = CommentActions;