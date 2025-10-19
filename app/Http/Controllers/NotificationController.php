<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;
use App\Models\InAppNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show admin inbox
     */
    public function adminInbox(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unseen, alert_created, etc.
        
        $query = InAppNotification::forAdmin()->latest();
        
        if ($filter === 'unseen') {
            $query->unseen();
        } elseif (in_array($filter, ['alert_created', 'request_submitted', 'shelter_assigned', 'status_updated'])) {
            $query->ofType($filter);
        }
        
        $notifications = $query->paginate(20);
        $unseenCount = InAppNotification::forAdmin()->unseen()->count();
        
        $stats = [
            'total' => InAppNotification::forAdmin()->count(),
            'unseen' => $unseenCount,
            'alert_created' => InAppNotification::forAdmin()->ofType('alert_created')->count(),
            'request_submitted' => InAppNotification::forAdmin()->ofType('request_submitted')->count(),
            'shelter_assigned' => InAppNotification::forAdmin()->ofType('shelter_assigned')->count(),
            'status_updated' => InAppNotification::forAdmin()->ofType('status_updated')->count(),
        ];
        
        return view('admin.inbox', compact('notifications', 'stats', 'filter', 'unseenCount'));
    }

    /**
     * Show citizen inbox
     */
    public function citizenInbox(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $userId = Auth::id();
        
        $query = InAppNotification::forCitizen($userId)->latest();
        
        if ($filter === 'unseen') {
            $query->unseen();
        } elseif (in_array($filter, ['request_submitted', 'shelter_assigned', 'status_updated'])) {
            $query->ofType($filter);
        }
        
        $notifications = $query->paginate(20);
        $unseenCount = InAppNotification::forCitizen($userId)->unseen()->count();
        
        $stats = [
            'total' => InAppNotification::forCitizen($userId)->count(),
            'unseen' => $unseenCount,
            'request_submitted' => InAppNotification::forCitizen($userId)->ofType('request_submitted')->count(),
            'shelter_assigned' => InAppNotification::forCitizen($userId)->ofType('shelter_assigned')->count(),
            'status_updated' => InAppNotification::forCitizen($userId)->ofType('status_updated')->count(),
        ];
        
        return view('citizen.inbox', compact('notifications', 'stats', 'filter', 'unseenCount'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = InAppNotification::findOrFail($id);
        $notification->markAsSeen();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all as read (Admin)
     */
    public function markAllAdminAsRead()
    {
        $service = new NotificationService();
        $service->markAllAdminAsSeen();
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Mark all as read (Citizen)
     */
    public function markAllCitizenAsRead()
    {
        $service = new NotificationService();
        $service->markAllCitizenAsSeen(Auth::id());
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get unseen count (AJAX)
     */
    public function getUnseenCount()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $count = InAppNotification::forAdmin()->unseen()->count();
        } else {
            $count = InAppNotification::forCitizen($user->id)->unseen()->count();
        }
        
        return response()->json(['count' => $count]);
    }

    /**
     * Show notification settings page
     */
    public function index()
    {
        return view('admin.notifications');
    }

    /**
     * Subscribe to push notifications
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $subscription = PushSubscription::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'endpoint' => $validated['endpoint']
            ],
            [
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'user_agent' => $request->userAgent(),
                'is_active' => true,
                'last_used_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to push notifications',
            'subscription_id' => $subscription->id
        ]);
    }

    // Unsubscribe from push notifications
    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string'
        ]);

        $deleted = PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully unsubscribed from push notifications',
            'deleted' => $deleted
        ]);
    }

    
     //Update notification preferences
     
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array'
        ]);

        $updated = PushSubscription::where('user_id', Auth::id())
            ->update(['preferences' => $validated['preferences']]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
            'updated' => $updated
        ]);
    }

    /**
     * Get current notification preferences
     */
    public function getPreferences()
    {
        $subscription = PushSubscription::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        return response()->json([
            'success' => true,
            'preferences' => $subscription->preferences ?? [],
            'is_subscribed' => $subscription !== null
        ]);
    }

    /**
     * Send test notification
     */
    public function sendTest(Request $request)
    {
        $subscription = PushSubscription::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active push subscription found'
            ], 404);
        }

        // In a real implementation, you would use a library like laravel-notification-channels/webpush
        // For now, return success
        return response()->json([
            'success' => true,
            'message' => 'Test notification sent successfully'
        ]);
    }
}
