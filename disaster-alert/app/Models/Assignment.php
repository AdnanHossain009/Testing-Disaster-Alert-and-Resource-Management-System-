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

    /// get the request for this assignment

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /// get the shelter for this assignment

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

   /// get the admin who assigned this assignment

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /// get status color for UI 

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

    /// check if assignment is active

    public function isActive(): bool
    {
        return in_array($this->status, ['Assigned', 'Checked In']);
    }

    /// check in the person

    public function checkIn(): void
    {
        $this->update([
            'status' => 'Checked In',
            'checked_in_at' => now()
        ]);

        // Updating shelter occupancy
        $this->shelter->updateOccupancy();
    }

    /// check out the person

    public function checkOut(): void
    {
        $this->update([
            'status' => 'Checked Out',
            'checked_out_at' => now()
        ]);

        // updating shelter occupancy
        $this->shelter->updateOccupancy();
    }

    /// cancel the assignment

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'Cancelled',
            'notes' => $reason
        ]);

        // update request status
        $this->request->update(['status' => 'Pending']);

        // update shelter occupancy
        $this->shelter->updateOccupancy();
    }

    /// get duration of stay in hours
    
    public function getStayDurationAttribute(): ?int
    {
        if (!$this->checked_in_at) return null;
        
        $endTime = $this->checked_out_at ?? now();
        return $this->checked_in_at->diffInHours($endTime);
    }

    /// scope for active assignments

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Assigned', 'Checked In']);
    }

    /// scope for assignments by status

    public function scopeAtShelter($query, $shelterId)
    {
        return $query->where('shelter_id', $shelterId);
    }

    //  scope for recent assignments
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('assigned_at', '>=', now()->subDays($days));
    }
}

