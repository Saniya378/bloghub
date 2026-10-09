<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Show the notifications of the signed in user.
     */
    public function index(): View
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);

        return view('pages.dashboard.notifications', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark every unread notification as read.
     */
    public function markAllRead(): RedirectResponse
    {
        $count = auth()->user()->unreadNotifications()->count();

        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', $count === 1 ? '1 notification marked as read.' : "{$count} notifications marked as read.");
    }
}