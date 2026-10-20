<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register route middleware aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'csrf' => \App\Http\Middleware\VerifyCsrfToken::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'rate.limit' => \App\Http\Middleware\RateLimit::class,
        ]);
        
        // Apply security headers to all responses
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? null;
                $message = $retryAfter
                    ? "Too many requests. Please try again in {$retryAfter} seconds."
                    : 'Too many requests. Please slow down.';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 429, $e->getHeaders());
            }
        });
    })->create();
