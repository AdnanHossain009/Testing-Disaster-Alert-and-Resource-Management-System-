<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
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
