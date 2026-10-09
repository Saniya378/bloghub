/**
 * BlogHub — Live Relative Timestamps
 *
 * Keeps every `[data-timestamp]` element in sync with the clock so labels tick
 * from "just now" to "1 sec ago" to "2 secs ago" without a page reload.
 *
 * The server renders the initial string (see <x-time-ago>), so the page is
 * correct before this script runs and stays correct if JavaScript is disabled.
 *
 * Rules must stay in sync with app/Support/RelativeTime.php.
 */

const RelativeTime = (function () {
    'use strict';

    const TICK_MS = 1000;

    let clockSkew = 0;
    let timer = null;
    let observer = null;

    /**
     * Difference between the server clock and the browser clock, in seconds.
     * Set from window.blogHubServerTime so labels never jump on visitors whose
     * device clock is wrong.
     */
    function syncClockSkew() {
        const serverNow = Number(window.blogHubServerTime);
        if (Number.isFinite(serverNow) && serverNow > 0) {
            clockSkew = serverNow - Math.floor(Date.now() / 1000);
        }
    }

    /**
     * "just now" / "5 sec ago" / "3 hrs ago" for a unix timestamp (seconds).
     */
    function format(timestamp, now) {
        const current = typeof now === 'number' ? now : Math.floor(Date.now() / 1000) + clockSkew;
        const seconds = current - timestamp;

        // Treat future timestamps as "just now" instead of negative ages.
        if (seconds < 5) return 'just now';

        if (seconds < 60) return plural(seconds, 'sec');

        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return plural(minutes, 'min');

        const hours = Math.floor(minutes / 60);
        if (hours < 24) return plural(hours, 'hr');

        const days = Math.floor(hours / 24);
        if (days < 7) return plural(days, 'day');
        if (days < 35) return plural(Math.floor(days / 7), 'wk');
        if (days < 365) return plural(Math.floor(days / 30), 'mo');

        return plural(Math.floor(days / 365), 'yr');
    }

    function plural(count, unit) {
        return count + ' ' + unit + (count === 1 ? '' : 's') + ' ago';
    }

    /**
     * Split a label into its static prefix and the relative part so refreshing
     * only rewrites "1 sec ago" and leaves "Liked " untouched.
     */
    function splitLabel(element) {
        if (element.dataset.timePrefix === undefined) {
            const text = element.textContent || '';
            const match = text.match(/^(.*?)((?:just now)|\d+ .*? ago)$/);
            element.dataset.timePrefix = match && match[1] ? match[1] : '';
        }

        return element.dataset.timePrefix;
    }

    /**
     * Update one element. Returns true when the text actually changed, so the
     * tick loop can skip untouched DOM writes.
     */
    function refresh(element) {
        const timestamp = Number(element.dataset.timestamp);

        if (!Number.isFinite(timestamp)) return false;

        const next = splitLabel(element) + format(timestamp);

        if (element.textContent !== next) {
            element.textContent = next;
            return true;
        }

        return false;
    }

    /**
     * Update every tracked element inside root (defaults to the document).
     */
    function refreshAll(root) {
        const scope = root && root.querySelectorAll ? root : document;
        const nodes = scope.querySelectorAll('[data-timestamp]');

        for (let i = 0; i < nodes.length; i++) {
            refresh(nodes[i]);
        }

        return nodes.length;
    }

    function tick() {
        refreshAll();
    }

    function start() {
        syncClockSkew();
        refreshAll();

        if (timer !== null) return;

        timer = setInterval(tick, TICK_MS);

        // Browsers throttle timers in background tabs, so resync the skew as
        // soon as the visitor comes back rather than trusting the stale one.
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                syncClockSkew();
                refreshAll();
            }
        });

        // Comment threads are re-rendered over AJAX, so watch for new nodes.
        if ('MutationObserver' in window) {
            observer = new MutationObserver((mutations) => {
                for (const mutation of mutations) {
                    for (const node of mutation.addedNodes) {
                        if (node.nodeType !== Node.ELEMENT_NODE) continue;

                        if (node.matches('[data-timestamp]')) {
                            refresh(node);
                        }

                        refreshAll(node);
                    }
                }
            });

            observer.observe(document.body, { childList: true, subtree: true });
        }
    }

    // This script is deferred on public pages and inline-loaded on the
    // dashboard, so DOMContentLoaded may already have fired by the time it runs.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }

    return { format, refresh, refreshAll, start };
})();

window.RelativeTime = RelativeTime;