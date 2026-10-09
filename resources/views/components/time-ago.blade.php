@props([
    'date',
    'prefix' => '',
    'class' => 'text-muted',
])

@php
    // Accept Carbon instances, plain date strings and raw unix timestamps.
    // Pivot models (bookmarks, article_likes, ...) hand back uncast strings,
    // so normalise before rendering.
    $carbon = match (true) {
        $date instanceof \DateTimeInterface => \Carbon\Carbon::instance(
            $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date->format('Y-m-d H:i:s.u'))
        ),
        is_int($date) => \Carbon\Carbon::createFromTimestamp($date),
        is_numeric($date) => \Carbon\Carbon::createFromTimestamp((int) $date),
        is_string($date) && trim($date) !== '' => rescue(
            fn () => \Carbon\Carbon::parse($date),
            null,
            false
        ),
        default => null,
    };

    $timestamp = $carbon?->getTimestamp();
@endphp

@if($timestamp !== null)
<time class="bh-time-ago {{ $class }}"
      datetime="{{ $carbon->toIso8601String() }}"
      data-timestamp="{{ $timestamp }}"
      title="{{ $carbon->format('M j, Y \a\t g:i A') }}">{{ $prefix }}{{ \App\Support\RelativeTime::format($timestamp) }}</time>
@else
<span class="bh-time-ago {{ $class }}" title="Unknown time">{{ $prefix }}&mdash;</span>
@endif