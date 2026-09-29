<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Redirect unauthenticated users to login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect('/login')->with('error', 'Please sign in to access this page.');
        }

        // Check if user account is still active
        if (!Auth::user()->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account deactivated.'], 403);
            }
            return redirect('/login')->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
