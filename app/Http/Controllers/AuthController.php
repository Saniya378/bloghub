<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('pages.login');
    }

    /**
     * Handle login request with server-side validation and rate limiting.
     */
    public function login(Request $request): RedirectResponse
    {
        // Rate limiting: max 5 attempts per minute per IP
        $key = 'login_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->withInput($request->except('password'));
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key);
            return back()->withErrors($validator)->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Find user by email or name
        $user = User::where('email', $credentials['email'])
            ->orWhere('name', $credentials['email'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check if user account is active
        if (!$user->isActive()) {
            RateLimiter::hit($key);
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ])->onlyInput('email');
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        Auth::login($user, $remember);

        // Regenerate the session to prevent session fixation
        $request->session()->regenerate();

        return redirect()->intended($user->dashboardUrl())->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Show the registration form.
     */
    public function showRegister(): View
    {
        return view('pages.register');
    }

    /**
     * Handle registration request with server-side validation.
     */
    public function register(Request $request): RedirectResponse
    {
        // Rate limiting: max 3 registrations per minute per IP
        $key = 'register_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many registration attempts. Please try again in {$seconds} seconds.",
            ])->withInput($request->except('password'));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ], [
            'name.regex' => 'The name may only contain letters, spaces, and hyphens.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key);
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => 'reader',
            'is_active' => true,
        ]);

        RateLimiter::clear($key);

        Auth::login($user);

        return redirect('/')->with('success', 'Welcome to BlogHub, ' . $user->name . '!');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }
}
