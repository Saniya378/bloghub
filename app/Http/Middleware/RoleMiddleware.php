<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Check if user has the required role.
     *
     * @param string|array $roles Allowed role(s)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect('/login')->with('error', 'Please sign in to access this page.');
        }

        if (!Auth::user()->isActive()) {
            Auth::logout();
            $request->session()->invalidate();

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account deactivated.'], 403);
            }
            return redirect('/login')->with('error', 'Your account has been deactivated.');
        }

        if (!empty($roles) && !in_array(Auth::user()->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Insufficient permissions.'], 403);
            }
            return redirect(Auth::user()->dashboardUrl())->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
