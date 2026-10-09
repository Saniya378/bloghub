<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Compact relative timestamps ("just now", "5 sec ago", "3 hrs ago").
 *
 * The rules here must stay in sync with
 * public/assets/js/relative-time.js, which recomputes the same strings in the
 * browser so timestamps keep ticking without a page reload.
 */
class RelativeTime
{
    /**
     * Human readable "time ago" label for a unix timestamp.
     *
     * Defaults to Carbon's "now" rather than time() so a frozen test clock
     * (or any future app level clock override) is respected.
     */
    public static function format(int $timestamp, ?int $now = null): string
    {
        $now ??= Carbon::now()->timestamp;

        // Clock skew between the server and the visitor must not produce
        // negative ages, so anything in the future reads as "just now".
        $seconds = $now - $timestamp;

        if ($seconds < 5) {
            return 'just now';
        }

        if ($seconds < 60) {
            return self::plural($seconds, 'sec');
        }

        $minutes = intdiv($seconds, 60);

        if ($minutes < 60) {
            return self::plural($minutes, 'min');
        }

        $hours = intdiv($minutes, 60);

        if ($hours < 24) {
            return self::plural($hours, 'hr');
        }

        $days = intdiv($hours, 24);

        if ($days < 7) {
            return self::plural($days, 'day');
        }

        if ($days < 35) {
            return self::plural(intdiv($days, 7), 'wk');
        }

        if ($days < 365) {
            return self::plural(intdiv($days, 30), 'mo');
        }

        return self::plural(intdiv($days, 365), 'yr');
    }

    /**
     * "1 sec ago" / "12 secs ago" — seconds and weeks use the plural form.
     */
    private static function plural(int $count, string $unit): string
    {
        return $count . ' ' . $unit . ($count === 1 ? '' : 's') . ' ago';
    }
}