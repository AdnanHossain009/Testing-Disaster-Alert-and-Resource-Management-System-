<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shelter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'capacity',
        'current_occupancy',
        'facilities',
        'contact_phone',
        'contact_email',
        'status',
        'special_notes'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'facilities' => 'array',
        'capacity' => 'integer',
        'current_occupancy' => 'integer',
    ];

    /**
     * Get all assignments for this shelter
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get active assignments for this shelter
     */
    public function activeAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class)->whereIn('status', ['Assigned', 'Checked In']);
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacityAttribute(): int
    {
        return max(0, $this->capacity - $this->current_occupancy);
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentageAttribute(): float
    {
        return $this->capacity > 0 ? ($this->current_occupancy / $this->capacity) * 100 : 0;
    }

    /**
     * Get status based on occupancy
     */
    public function getAvailabilityStatusAttribute(): string
    {
        $percentage = $this->occupancy_percentage;
        
        if ($percentage >= 100) return 'Full';
        if ($percentage >= 90) return 'Nearly Full';
        if ($percentage >= 70) return 'Limited';
        return 'Available';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->availability_status) {
            'Available' => '#27ae60',
            'Limited' => '#f39c12',
            'Nearly Full' => '#e67e22',
            'Full' => '#e74c3c',
            default => '#95a5a6'
        };
    }

    /**
     * Check if shelter can accommodate people
     */
    public function canAccommodate(int $people): bool
    {
        return $this->status === 'Active' && $this->available_capacity >= $people;
    }

    /**
     * Calculate distance from coordinates (in kilometers)
     */
    public function distanceFrom(float $latitude, float $longitude): float
    {
        if (!$this->latitude || !$this->longitude) {
            return PHP_FLOAT_MAX;
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($latitude - $this->latitude);
        $lonDelta = deg2rad($longitude - $this->longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Scope for active shelters
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for available shelters (with capacity)
     */
    public function scopeAvailable($query, $peopleCount = 1)
    {
        return $query->active()
                    ->whereRaw('capacity - current_occupancy >= ?', [$peopleCount]);
    }

    /**
     * Scope for shelters in city
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Update occupancy count
     */
    public function updateOccupancy(): void
    {
        $activeCount = $this->activeAssignments()
            ->whereIn('status', ['Assigned', 'Checked In'])
            ->count();
            
        $this->update(['current_occupancy' => $activeCount]);
    }
}
