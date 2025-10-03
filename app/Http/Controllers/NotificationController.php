<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display the notifications page.
     */
    public function index(): View
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Group notifications by date
        $groupedNotifications = $notifications->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d');
        });

        return view('pages.notifications.index', compact('notifications', 'groupedNotifications'));
    }

    /**
     * Get unread notifications for popup.
     */
    public function unread(): JsonResponse
    {
        $notifications = Auth::user()
            ->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->notifications()->unread()->count()
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        // Ensure user can only mark their own notifications as read
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()
            ->notifications()
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
