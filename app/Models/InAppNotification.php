<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InAppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_type',
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'reference_id',
        'reference_type',
        'seen',
        'seen_at',
    ];

    protected $casts = [
        'seen' => 'boolean',
        'seen_at' => 'datetime',
    ];

    /**
     * Get the user this notification belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as seen
     */
    public function markAsSeen(): void
    {
        $this->update([
            'seen' => true,
            'seen_at' => now(),
        ]);
    }

    /**
     * Scope for unseen notifications
     */
    public function scopeUnseen($query)
    {
        return $query->where('seen', false);
    }

    /**
     * Scope for admin notifications
     */
    public function scopeForAdmin($query)
    {
        return $query->where('recipient_type', 'admin');
    }

    /**
     * Scope for citizen notifications
     */
    public function scopeForCitizen($query, $userId)
    {
        return $query->where('recipient_type', 'citizen')
                    ->where('user_id', $userId);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get time ago string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
