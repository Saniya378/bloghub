<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * CSRF token validation for all state-changing requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate state-changing methods
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
            
            if (!$token || !hash_equals((string) $request->session()->token(), (string) $token)) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'CSRF token mismatch.'], 419);
                }
                
                return back()->withErrors(['error' => 'Invalid CSRF token. Please refresh the page and try again.']);
            }
        }

        return $next($request);
    }
}
