<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'severity',
        'type',
        'location',
        'latitude',
        'longitude',
        'status',

        'issued_at',
        'expires_at',
        'created_by'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    
    /// get the user who created this alert

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /// check if alert is currently active

    public function isActive(): bool
    {
        return $this->status === 'Active' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /// check if alert is expired

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /// get severity color for UI

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'Low' => '#27ae60',
            'Moderate' => '#f39c12',
            'High' => '#e67e22',
            'Critical' => '#e74c3c',
            default => '#95a5a6'
        };
    }

    /// get type icon for UI

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'Flood' => '🌊',
            'Earthquake' => '🌍',
            'Cyclone' => '🌪️',
            'Fire' => '🔥',
            'Health Emergency' => '🏥',
            default => '⚠️'
        };
    }

    /// scope for active alerts

    public function scopeActive($query)
    {
        return $query->where('status', 'Active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /// scope for alerts by severity

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /// scope for recent alerts (issued within last N days)


    public function scopeRecent($query, $days = 7)
    {
        return $query->where('issued_at', '>=', now()->subDays($days));
    }
}
