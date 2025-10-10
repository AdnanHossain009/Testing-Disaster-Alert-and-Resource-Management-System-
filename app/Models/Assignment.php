<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'shelter_id',
        'assigned_by',
        'assigned_at',
        'checked_in_at',
        'checked_out_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    /**
     * Get the request for this assignment
     */

    public function request(): BelongsTo
    {
        return $this->belongsTo(HelpRequest::class, 'request_id');
    }

    /**
     * Get the shelter for this assignment
     */

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * Get the user who made the assignment
     */

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get status color for UI
     */

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Assigned' => '#3498db',
            'Checked In' => '#27ae60',
            'Checked Out' => '#95a5a6',
            'Cancelled' => '#e74c3c',
            default => '#95a5a6'
        };
    }

    /**
     * Check if assignment is active
     */

    public function isActive(): bool
    {
        return in_array($this->status, ['Assigned', 'Checked In']);
    }

    /**
     * Check in the person
     */

    public function checkIn(): void
    {
        $this->update([
            'status' => 'Checked In',
            'checked_in_at' => now()
        ]);

        // Update shelter occupancy
        $this->shelter->updateOccupancy();
    }

    /**
     * Check out the person
     */

    public function checkOut(): void
    {
        $this->update([
            'status' => 'Checked Out',
            'checked_out_at' => now()
        ]);

        // Update shelter occupancy
        $this->shelter->updateOccupancy();
    }

    /**
     * Cancel the assignment
     */

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'Cancelled',
            'notes' => $reason
        ]);

        // Update request status - using the correct relationship
        $helpRequest = HelpRequest::find($this->request_id);
        if ($helpRequest) {
            $helpRequest->update(['status' => 'Pending']);
        }

        // Update shelter occupancy
        $this->shelter->updateOccupancy();
    }

    /**
     * Get duration of stay
     */
    
    public function getStayDurationAttribute(): ?int
    {
        if (!$this->checked_in_at) return null;
        
        $endTime = $this->checked_out_at ?? now();
        return $this->checked_in_at->diffInHours($endTime);
    }

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Assigned', 'Checked In']);
    }

    /**
     * Scope for assignments at a specific shelter
     */
    public function scopeAtShelter($query, $shelterId)
    {
        return $query->where('shelter_id', $shelterId);
    }

    /**
     * Scope for recent assignments
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('assigned_at', '>=', now()->subDays($days));
    }
}

