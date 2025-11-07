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
        $notificationsQuery = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc');

        $notifications = $this->applyBagianFilter($notificationsQuery)->paginate(20);

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
        $unreadQuery = Auth::user()
            ->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(5);

        $notifications = $this->applyBagianFilter($unreadQuery)->get();

        $unreadCountQuery = Auth::user()
            ->notifications()
            ->unread();

        $unreadCount = $this->applyBagianFilter($unreadCountQuery)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
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

    /**
     * ANCHOR: Apply bagian filter for non-admin users.
     * Ensure notifications are restricted to the logged-in user's bagian unless the user is an admin.
     */
    private function applyBagianFilter($query)
    {
        $user = Auth::user();

        if (!$user || $user->role === 'Admin' || !$user->bagian_id) {
            return $query;
        }

        return $query->where('data->bagian_id', $user->bagian_id);
    }
}
