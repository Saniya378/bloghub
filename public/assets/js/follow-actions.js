/**
 * BlogHub — Follow Buttons
 *
 * Handles every `.follow-btn[data-author-id]` on the page via delegation, so
 * buttons rendered or replaced later keep working.
 *
 * Contract: POST /authors/{id}/follow responds with
 *   { success: true, following: bool, followers: int }
 */

const FollowActions = (function () {
    'use strict';

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

    function isAuthenticated() {
        return window.isAuthenticated === true;
    }

    /** Repaint a button from a given following/followers state. */
    function paint(button, following, followers) {
        button.dataset.following = following ? '1' : '0';
        button.setAttribute('aria-pressed', String(!!following));
        button.classList.toggle('btn-primary', !!following);
        button.classList.toggle('btn-outline-primary', !following);
        button.disabled = false;

        const size = button.classList.contains('btn-lg') ? 'btn-lg' : 'btn-sm';

        button.innerHTML = following
            ? '<i class="fa-solid fa-check me-1"></i> Following'
            : '<i class="fa-solid fa-plus me-1"></i> Follow';

        button.classList.remove('btn-lg', 'btn-sm');
        button.classList.add(size);

        // Any element that opted in to showing this author's follower count
        document.querySelectorAll('[data-follower-count-for="' + button.dataset.authorId + '"]')
            .forEach((el) => { el.textContent = followers; });
    }

    async function toggle(button) {
        if (!isAuthenticated()) {
            promptLogin();
            return;
        }

        const authorId = button.dataset.authorId;

        if (!authorId) return;

        const wasFollowing = button.dataset.following === '1';

        // Optimistic flip
        button.disabled = true;
        paint(button, !wasFollowing, null);

        try {
            const response = await fetch('/authors/' + authorId + '/follow', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            });

            const data = await response.json();

            if (!response.ok || data.error) {
                // Roll back to the real previous state
                paint(button, wasFollowing, null);
                toast('Error', data.error || 'Could not update follow.', 'fa-solid fa-triangle-exclamation text-danger');
                return;
            }

            paint(button, !!data.following, data.followers);

            // A button inside a "who I follow" list drops its whole row when
            // the viewer unfollows that author.
            if (button.dataset.removeRow === '1') {
                const row = button.closest('[data-following-row]');

                if (row) {
                    if (data.following) {
                        // Re-followed: bring the row back
                        row.classList.remove('d-none');
                    } else {
                        row.style.transition = 'opacity .2s ease';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 200);
                    }
                }
            }

            toast(
                data.following ? 'Following' : 'Unfollowed',
                data.following ? 'This author was added to your Following list.' : '',
                data.following ? 'fa-solid fa-user-check' : 'fa-solid fa-user-minus'
            );

            // Let other parts of the UI (e.g. the Following tab) react
            document.dispatchEvent(new CustomEvent('bloghub:follow-change', {
                detail: {
                    authorId: Number(authorId),
                    following: !!data.following,
                    followers: data.followers
                }
            }));
        } catch (error) {
            paint(button, wasFollowing, null);
            toast('Error', 'Something went wrong. Please try again.', 'fa-solid fa-triangle-exclamation text-danger');
        }
    }

    function init() {
        // One delegated listener covers every present and future button
        if (document.body.dataset.followActionsBound === '1') return;
        document.body.dataset.followActionsBound = '1';

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.follow-btn');
            if (!button) return;

            event.preventDefault();
            toggle(button);
        });
    }

    document.addEventListener('DOMContentLoaded', init);

    return { init, toggle, paint };
})();

window.FollowActions = FollowActions;