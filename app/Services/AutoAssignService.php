<?php

namespace App\Services;

use App\Models\Request;
use App\Models\Assignment;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoAssignService
{
    /**
     * Auto-assign pending requests to nearest available shelters
     * when admin is inactive
     * 
     * @param int $inactivityMinutes Admin inactivity threshold (default: 10)
     * @return array Assignment results
     */
    public function assignPendingRequests(int $inactivityMinutes = 10): array
    {
        $results = [
            'checked_at' => now()->toDateTimeString(),
            'admin_inactive' => false,
            'pending_requests' => 0,
            'assigned' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Check if admin is inactive
            if (!$this->isAdminInactive($inactivityMinutes)) {
                $results['message'] = 'Admin is active. Auto-assignment not needed.';
                return $results;
            }

            $results['admin_inactive'] = true;

            // Get pending requests that haven't been assigned yet (no assignment record)
            $pendingRequests = Request::where('status', 'Pending')
                ->whereDoesntHave('assignment')
                ->get();

            $results['pending_requests'] = $pendingRequests->count();

            if ($pendingRequests->isEmpty()) {
                $results['message'] = 'No pending requests to assign.';
                return $results;
            }

            // Get system admin for auto-assignment
            $systemAdmin = User::where('role', 'admin')->first();
            
            if (!$systemAdmin) {
                $results['message'] = 'No admin user found for auto-assignment.';
                Log::error('No admin user found for auto-assignment');
                return $results;
            }

            // Process each pending request
            foreach ($pendingRequests as $request) {
                try {
                    $shelter = $this->findNearestAvailableShelter($request);

                    if ($shelter) {
                        // Create assignment using the model method
                        $assignment = $request->assignToShelter($shelter, $systemAdmin);
                        
                        // Update admin notes
                        $request->update([
                            'admin_notes' => 'Auto-assigned by system (Admin inactive)'
                        ]);

                        // Update shelter occupancy
                        $peopleCount = $request->people_count ?? 1;
                        $shelter->increment('current_occupancy', $peopleCount);

                        $results['assigned']++;

                        Log::info("Auto-assigned Request #{$request->id} to Shelter #{$shelter->id}", [
                            'request_id' => $request->id,
                            'shelter_id' => $shelter->id,
                            'shelter_name' => $shelter->name,
                            'people_count' => $peopleCount
                        ]);

                        // TODO: Send notification to citizen about assignment
                        // event(new RequestAssigned($request));

                    } else {
                        $results['failed']++;
                        $results['errors'][] = "Request #{$request->id}: No available shelter found";
                        
                        Log::warning("Auto-assignment failed for Request #{$request->id}: No available shelter", [
                            'request_id' => $request->id,
                            'location' => $request->location
                        ]);
                    }

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Request #{$request->id}: {$e->getMessage()}";
                    
                    Log::error("Auto-assignment error for Request #{$request->id}", [
                        'request_id' => $request->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $results['message'] = "Auto-assignment complete. Assigned: {$results['assigned']}, Failed: {$results['failed']}";

        } catch (\Exception $e) {
            $results['errors'][] = "Service error: {$e->getMessage()}";
            Log::error('AutoAssignService error', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Check if admin is inactive based on last activity
     * 
     * @param int $minutes Inactivity threshold in minutes
     * @return bool
     */
    protected function isAdminInactive(int $minutes): bool
    {
        $threshold = Carbon::now()->subMinutes($minutes);

        // Check if any admin user has been active within threshold
        $activeAdmin = User::where('role', 'admin')
            ->where('last_activity', '>=', $threshold)
            ->exists();

        return !$activeAdmin;
    }

    /**
     * Find nearest available shelter for a request
     * 
     * @param Request $request
     * @return Shelter|null
     */
    protected function findNearestAvailableShelter(Request $request): ?Shelter
    {
        // Get request coordinates
        $requestLat = $request->latitude;
        $requestLng = $request->longitude;

        // Get people count (default to 1 if not specified)
        $peopleCount = $request->people_count ?? 1;

        // If coordinates are not available, return first available shelter
        if (!$requestLat || !$requestLng) {
            return $this->getFirstAvailableShelter($peopleCount);
        }

        // Find nearest shelter with capacity using Haversine formula
        $nearestShelter = Shelter::where('status', 'Active')
            ->whereRaw('(capacity - current_occupancy) >= ?', [$peopleCount])
            ->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(latitude))
                )) AS distance
            ", [$requestLat, $requestLng, $requestLat])
            ->orderBy('distance', 'asc')
            ->first();

        // If no nearby shelter found, try any available shelter
        if (!$nearestShelter) {
            return $this->getFirstAvailableShelter($peopleCount);
        }

        return $nearestShelter;
    }

    /**
     * Get first available shelter with sufficient capacity
     * 
     * @param int $peopleCount
     * @return Shelter|null
     */
    protected function getFirstAvailableShelter(int $peopleCount): ?Shelter
    {
        return Shelter::where('status', 'Active')
            ->whereRaw('(capacity - current_occupancy) >= ?', [$peopleCount])
            ->orderBy('current_occupancy', 'asc') // Prefer less crowded shelters
            ->first();
    }

    /**
     * Get auto-assignment statistics
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        $pendingCount = Request::where('status', 'Pending')
            ->whereDoesntHave('assignment')
            ->count();

        $availableShelters = Shelter::where('status', 'Active')
            ->whereRaw('capacity > current_occupancy')
            ->count();

        $totalCapacity = Shelter::where('status', 'Active')
            ->sum(DB::raw('capacity - current_occupancy'));

        $lastAdminActivity = User::where('role', 'admin')
            ->max('last_activity');

        return [
            'pending_requests' => $pendingCount,
            'available_shelters' => $availableShelters,
            'total_available_capacity' => $totalCapacity,
            'last_admin_activity' => $lastAdminActivity 
                ? Carbon::parse($lastAdminActivity)->diffForHumans() 
                : 'Never',
            'admin_status' => $this->isAdminInactive(10) ? 'Inactive' : 'Active'
        ];
    }
}
