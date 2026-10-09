/**
 * BlogHub — Toggle Actions Module
 * Handles like, bookmark, and follow with optimistic UI and rollback
 */

const ToggleActions = (function() {
    'use strict';

    /**
     * Show login modal for guests
     */
    function showLoginModal() {
        const modalHtml = `
            <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Sign In Required</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <i class="fa-solid fa-lock fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Please sign in to interact with content.</p>
                            <a href="/login" class="btn btn-primary me-2">Sign In</a>
                            <a href="/register" class="btn btn-outline-primary">Create Account</a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existing = document.getElementById('loginModal');
        if (existing) existing.remove();

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('loginModal'));
        modal.show();
    }

    /**
     * Check if user is authenticated
     */
    function isAuthenticated() {
        return window.isAuthenticated === true;
    }

    /**
     * Perform toggle action with optimistic UI
     */
    async function toggle(action, id, options = {}) {
        const { button, icon, countEl, animationClass } = options;

        if (!isAuthenticated()) {
            showLoginModal();
            return;
        }

        // Optimistic UI update
        const previousState = {
            iconClass: icon ? icon.className : '',
            count: countEl ? countEl.textContent : '',
            btnClass: button ? button.className : ''
        };

        // Apply optimistic changes
        if (icon) {
            icon.classList.toggle('fa-solid');
            icon.classList.toggle('fa-regular');
        }
        if (countEl && !isNaN(parseInt(countEl.textContent))) {
            const current = parseInt(countEl.textContent);
            const isActive = icon && icon.classList.contains('fa-solid');
            countEl.textContent = isActive ? current + 1 : current - 1;
        }
        if (animationClass && icon) {
            icon.classList.add(animationClass);
            setTimeout(() => icon.classList.remove(animationClass), 300);
        }

        try {
            // Follows are scoped to authors, everything else to articles.
            const url = action === 'follow'
                ? `/authors/${id}/follow`
                : `/articles/${id}/${action}`;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Request failed');
            }

            // Roll the optimistic state back if the server disagreed
            if (data.liked !== undefined) {
                const isActive = !!data.liked;
                icon?.classList.toggle('fa-solid', isActive);
                icon?.classList.toggle('fa-regular', !isActive);
                button?.setAttribute?.('aria-pressed', String(isActive));
            }
            if (data.bookmarked !== undefined) {
                const isActive = !!data.bookmarked;
                icon?.classList.toggle('fa-solid', isActive);
                icon?.classList.toggle('fa-regular', !isActive);
                button?.setAttribute?.('aria-pressed', String(isActive));
            }
            if (data.following !== undefined) {
                const isActive = !!data.following;
                icon?.classList.toggle('fa-solid', isActive);
                icon?.classList.toggle('fa-regular', !isActive);
                button?.setAttribute?.('aria-pressed', String(isActive));
            }

            // Update with server response
            if (countEl && data.likes !== undefined) {
                countEl.textContent = data.likes;
            }
            if (countEl && data.followers !== undefined) {
                countEl.textContent = data.followers;
            }

            // Show success toast
            const toastMsg = action === 'like' ? (data.liked ? 'Liked' : 'Unliked') :
                           action === 'bookmark' ? (data.bookmarked ? 'Bookmarked' : 'Removed') :
                           (data.following ? 'Following' : 'Unfollowed');
            showToast(toastMsg, '', 'fa-solid fa-check');

        } catch (error) {
            // Rollback optimistic changes
            if (icon) {
                icon.className = previousState.iconClass;
            }
            if (countEl) {
                countEl.textContent = previousState.count;
            }

            // Show error toast
            showToast('Error', error.message || 'Something went wrong. Please try again.', 'fa-solid fa-exclamation-circle text-danger');
        }
    }

    /**
     * Show toast notification
     */
    function showToast(title, message, iconClass = 'fa-solid fa-circle-info') {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast show align-items-center text-bg-light border-0 shadow';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${iconClass} me-2"></i>
                    <strong>${title}</strong>${message ? ' — ' + message : ''}
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    /**
     * Initialize toggle buttons on the page
     */
    function init() {
        // Like buttons
        document.querySelectorAll('[data-action="like"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const icon = btn.querySelector('i');
                const countEl = btn.querySelector('.count');
                toggle('like', id, { button: btn, icon, countEl, animationClass: 'animate-like' });
            });
        });

        // Bookmark buttons
        document.querySelectorAll('[data-action="bookmark"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const icon = btn.querySelector('i');
                toggle('bookmark', id, { button: btn, icon });
            });
        });

        // Follow buttons
        document.querySelectorAll('[data-action="follow"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const icon = btn.querySelector('i');
                const countEl = btn.querySelector('.count');
                toggle('follow', id, { button: btn, icon, countEl });
            });
        });
    }

    return { init, toggle, showLoginModal, isAuthenticated };
})();

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    ToggleActions.init();
});

// Expose globally
window.ToggleActions = ToggleActions;
