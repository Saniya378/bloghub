<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile settings page.
     */
    public function show(): View
    {
        $user = Auth::user();

        // Authors this user follows, newest follow first.
        $following = $user->follows()
            ->withCount(['articles', 'followers'])
            ->orderByDesc('follows.created_at')
            ->paginate(12);

        return view('pages.profile-settings', [
            'user' => $user,
            'following' => $following,
            'followingTotal' => $user->follows()->count(),
        ]);
    }

    /**
     * Update profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'bio' => 'nullable|string|max:1000',
            'tagline' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'social.twitter' => 'nullable|url|max:255',
            'social.github' => 'nullable|url|max:255',
            'social.linkedin' => 'nullable|url|max:255',
        ], [
            'name.regex' => 'The name may only contain letters, spaces, and hyphens.',
            'avatar.image' => 'The avatar must be an image file.',
            'avatar.max' => 'The avatar must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->input('name'),
            'bio' => $request->input('bio'),
            'tagline' => $request->input('tagline'),
            'specialty' => $request->input('specialty'),
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = Storage::url($path);
        }

        // Handle social links
        $social = $user->social ?? [];
        $social['twitter'] = $request->input('social.twitter');
        $social['github'] = $request->input('social.github');
        $social['linkedin'] = $request->input('social.linkedin');
        $data['social'] = array_filter($social);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        // Rate limiting: max 3 attempts per minute
        $key = 'change_password_' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'current_password' => 'Too many attempts. Please try again later.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'new_password_confirmation' => 'required|string',
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key);
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            RateLimiter::hit($key);
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update(['password' => $request->input('new_password')]);

        RateLimiter::clear($key);

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Upgrade reader to author.
     */
    public function becomeAuthor(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role !== 'reader') {
            return back()->with('error', 'Only readers can become authors.');
        }

        $user->update(['role' => 'author']);

        return redirect('/dashboard')->with('success', 'Welcome to the author community! You can now publish articles.');
    }

    /**
     * Show user bookmarks.
     */
    public function bookmarks(): View
    {
        $bookmarks = Auth::user()->bookmarks()
            ->with('article.user')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('pages.bookmarks', compact('bookmarks'));
    }

    /**
     * Show user dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        $stats = [
            'articles' => $user->articles()->count(),
            'published' => $user->articles()->published()->count(),
            'drafts' => $user->articles()->draft()->count(),
            'total_views' => $user->articles()->sum('views'),
            'total_likes' => $user->articles()->sum('likes'),
            'followers' => $user->followers()->count(),
        ];

        $recentArticles = $user->articles()
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboard', compact('user', 'stats', 'recentArticles'));
    }
}
