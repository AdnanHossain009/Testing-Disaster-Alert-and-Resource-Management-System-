<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'request_type',
        'description',
        'location',
        'latitude',
        'longitude',
        'people_count',
        'urgency',
        'status',
        'special_needs',
        'assigned_at',
        'assigned_by',
        'admin_notes'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'people_count' => 'integer',
        'assigned_at' => 'datetime',
    ];

    //  get the user who made this request
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /// get the admin who assigned this request
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /// get the assignment for this request
    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    /// get urgency color for UI
    public function getUrgencyColorAttribute(): string
    {
        return match($this->urgency) {
            'Low' => '#27ae60',
            'Medium' => '#f39c12',
            'High' => '#e67e22',
            'Critical' => '#e74c3c',
            default => '#95a5a6'
        };
    }

    /// get status color for UI
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Pending' => '#f39c12',
            'Assigned' => '#3498db',
            'In Progress' => '#9b59b6',
            'Completed' => '#27ae60',
            'Cancelled' => '#95a5a6',
            default => '#95a5a6'
        };
    }

    /// get request type icon
    public function getTypeIconAttribute(): string
    {
        return match($this->request_type) {
            'Shelter' => '🏠',
            'Medical' => '🏥',
            'Food' => '🍽️',
            'Water' => '💧',
            'Rescue' => '🚁',
            default => '📋'
        };
    }

    /// check if request is pending
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /// check if request is assigned
    public function isAssigned(): bool
    {
        return in_array($this->status, ['Assigned', 'In Progress']);
    }

    /// check if request is completed
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    /// assign to shelter
    public function assignToShelter(Shelter $shelter, User $assignedBy): Assignment
    {
        $this->update([
            'status' => 'Assigned',
            'assigned_at' => now(),
            'assigned_by' => $assignedBy->id
        ]);

        return Assignment::create([
            'request_id' => $this->id,
            'shelter_id' => $shelter->id,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
            'status' => 'Assigned'
        ]);
    }

    /// scope for pending requests
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /// scope for urgent requests
    public function scopeUrgent($query)
    {
        return $query->whereIn('urgency', ['High', 'Critical']);
    }

    /// scope for requests by type
    public function scopeByType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    /// scope for recent requests
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
