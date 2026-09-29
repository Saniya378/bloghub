<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimit
{
    /**
     * Rate limiting middleware.
     * 
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $decaySeconds Time window in seconds
     */
    public function __construct(
        private int $maxAttempts = 60,
        private int $decaySeconds = 60
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'rate_limit_' . $request->ip() . '_' . $request->path();

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Too many requests.',
                    'retry_after' => $retryAfter,
                ], 429)->header('Retry-After', $retryAfter);
            }

            return back()->withErrors([
                'error' => 'Too many requests. Please try again in ' . $retryAfter . ' seconds.',
            ]);
        }

        RateLimiter::hit($key, $this->decaySeconds);

        return $next($request);
    }
}
