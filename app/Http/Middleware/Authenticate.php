<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Redirect unauthenticated users or return JSON for APIs.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guard = empty($guards) ? null : $guards[0];
        $authGuard = Auth::guard($guard);

        if (!$authGuard->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return redirect('/login')->with('error', 'Please sign in to access this page.');
        }

        $user = $authGuard->user();

        // Set the active guard and user resolver for the request
        if ($guard) {
            Auth::shouldUse($guard);
            $request->setUserResolver(fn ($g = null) => Auth::guard($g ?? $guard)->user());
        }

        // Check if user account is still active
        if ($user && method_exists($user, 'isActive') && !$user->isActive()) {
            if ($guard === 'web' || empty($guard)) {
                Auth::logout();
                $request->session()->invalidate();
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account deactivated.',
                ], 403);
            }
            return redirect('/login')->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
