/**
 * BlogHub — Live Article Feed
 *
 * Polls /api/posts/feed for articles published after the highest id already on
 * the page and prepends them, so a post an author just published shows up
 * without the reader refreshing.
 *
 * Attach to any container with:
 *   data-live-feed
 *   data-live-cursor="<highest article id on the page>"
 *   data-live-interval="20000"   (optional, default 20s)
 */

const LiveFeed = (function () {
    'use strict';

    const DEFAULT_INTERVAL = 20000;

    let container = null;
    let cursor = 0;
    let interval = DEFAULT_INTERVAL;
    let timer = null;
    let inFlight = false;
    let seenIds = new Set();

    function toast(title, message, icon) {
        if (typeof window.showToast === 'function') {
            window.showToast(title, message, icon || 'fa-solid fa-circle-info');
        }
    }

    function isAuthenticated() {
        return window.isAuthenticated === true;
    }

    function toastHost() {
        return document.getElementById('toastContainer');
    }

    /** A single "N new posts" pill the reader can click. */
    function showNewPostsBanner(count, newestTitle) {
        if (!container) return;

        let banner = document.getElementById('liveFeedBanner');

        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'liveFeedBanner';
            banner.className = 'live-feed-banner';
            banner.setAttribute('role', 'status');
            banner.innerHTML =
                '<i class="fa-solid fa-bolt me-2"></i>' +
                '<span class="live-feed-banner__label"></span>' +
                '<i class="fa-solid fa-chevron-down ms-2"></i>';

            banner.addEventListener('click', () => {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                banner.remove();
            });

            container.parentElement.insertBefore(banner, container);
        }

        const label = count === 1
            ? '1 new post — ' + (newestTitle || 'just published')
            : count + ' new posts';

        banner.querySelector('.live-feed-banner__label').textContent = label;
    }

    function prepend(html, articles) {
        if (!html || !container) return;

        // Freshly rendered cards must animate in
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;

        const columns = wrapper.querySelectorAll(':scope > *');

        container.insertBefore(wrapper.firstElementChild, container.firstChild);

        columns.forEach((col, index) => {
            col.classList.add('live-feed-new');

            if (index === 0) {
                setTimeout(() => col.classList.remove('live-feed-new'), 2500);
            }
        });

        // Wire up any handlers that depend on nodes added after load
        if (typeof window.ToggleActions?.init === 'function') {
            window.ToggleActions.init();
        }

        if (typeof window.RelativeTime?.refreshAll === 'function') {
            window.RelativeTime.refreshAll(container);
        }
    }

    async function poll() {
        if (inFlight || !container) return;

        inFlight = true;

        try {
            const response = await fetch('/api/posts/feed?after=' + encodeURIComponent(cursor), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) return;

            const data = await response.json();

            if (!data || !data.count) return;

            // Guard against a duplicate render if two polls overlap
            const fresh = (data.articles || []).filter((a) => !seenIds.has(a.id));

            if (!fresh.length) {
                cursor = data.cursor || cursor;
                return;
            }

            fresh.forEach((a) => seenIds.add(a.id));
            cursor = data.cursor || cursor;

            prepend(data.html, fresh);

            toast(
                data.count === 1 ? 'New post published' : data.count + ' new posts',
                fresh[0].author + ' just published “' + fresh[0].title + '”',
                'fa-solid fa-feather-pointed'
            );

            showNewPostsBanner(fresh.length, fresh[0].title);
        } catch (error) {
            // A failed poll is not worth surfacing; the next tick retries.
        } finally {
            inFlight = false;
        }
    }

    function start() {
        container = document.querySelector('[data-live-feed]');

        if (!container) return;

        cursor = parseInt(container.dataset.liveCursor, 10) || 0;
        interval = parseInt(container.dataset.liveInterval, 10) || DEFAULT_INTERVAL;

        // Remember what is already on screen so nothing is duplicated
        container.querySelectorAll('[data-article-id]').forEach((el) => {
            const id = parseInt(el.dataset.articleId, 10);
            if (id) seenIds.add(id);
        });

        if (isAuthenticated()) {
            timer = setInterval(poll, interval);

            // Background tabs get throttled; catch up as soon as they return.
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') poll();
            });
        }
    }

    function stop() {
        if (timer !== null) {
            clearInterval(timer);
            timer = null;
        }
    }

    document.addEventListener('DOMContentLoaded', start);

    return { start, stop, poll };
})();

window.LiveFeed = LiveFeed;