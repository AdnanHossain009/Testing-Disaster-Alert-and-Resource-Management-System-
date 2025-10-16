<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'user_agent',
        'preferences',
        'is_active',
        'last_used_at'
    ];

    protected $casts = [
        'preferences' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription should receive notification based on preferences
     */
    public function shouldReceiveNotification($type, $priority = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $prefs = $this->preferences ?? [];

        // Check Do Not Disturb
        if (($prefs['dndEnabled'] ?? false) && $this->isInQuietHours()) {
            // Allow critical during DND if configured
            if (!($prefs['dndAllowCritical'] ?? true) || $priority !== 'Critical') {
                return false;
            }
        }

        // Check if critical only mode is enabled
        if (($prefs['notifyCriticalOnly'] ?? false) && $priority !== 'Critical') {
            return false;
        }

        // Check notification types
        if (isset($prefs['notificationTypes']) && is_array($prefs['notificationTypes'])) {
            return in_array($type, $prefs['notificationTypes']);
        }

        return true;
    }

    /**
     * Check if current time is within quiet hours
     */
    protected function isInQuietHours(): bool
    {
        $prefs = $this->preferences ?? [];
        
        if (!isset($prefs['dndStart']) || !isset($prefs['dndEnd'])) {
            return false;
        }

        $now = now();
        $start = \Carbon\Carbon::createFromTimeString($prefs['dndStart']);
        $end = \Carbon\Carbon::createFromTimeString($prefs['dndEnd']);

        // Handle overnight quiet hours (e.g., 22:00 to 08:00)
        if ($end->lessThan($start)) {
            return $now->greaterThanOrEqualTo($start) || $now->lessThan($end);
        }

        return $now->between($start, $end);
    }
}
