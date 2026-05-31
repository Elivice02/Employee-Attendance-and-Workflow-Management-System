<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->appNotifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update([
            'read' => true,
            'read_at' => now(),
        ]);

        return $notification->action_url
            ? redirect($notification->action_url)
            : back();
    }

    public function markAllRead()
    {
        Auth::user()
            ->appNotifications()
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Notifications marked as read.');
    }
}
