<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as EmergencyRequest;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /**
     * Display all citizen requests (Admin view)
     */
    public function adminIndex()
    {
        // Get requests from database with relationships
        $requests = EmergencyRequest::with(['user', 'assignedBy'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'citizen_name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'location' => $request->location,
                    'emergency_type' => $request->request_type,
                    'description' => $request->description,
                    'status' => $request->status,
                    'urgency' => $request->urgency,
                    'people_count' => $request->people_count,
                    'special_needs' => $request->special_needs,
                    'created_at' => $request->created_at->format('Y-m-d H:i:s'),
                    'assigned_at' => $request->assigned_at ? $request->assigned_at->format('Y-m-d H:i:s') : null,
                    'assigned_by_name' => $request->assignedBy ? $request->assignedBy->name : null,
                    'priority' => $request->urgency, // Map urgency to priority for consistency
                    'assigned_shelter' => null, // TODO: Implement shelter assignment
                    'shelter_id' => null,
                    'assignment_type' => $request->assigned_at ? 'Manual' : null
                ];
            })->toArray();

        // Calculate statistics
        $stats = [
            'total_requests' => EmergencyRequest::count(),
            'pending_requests' => EmergencyRequest::where('status', 'Pending')->count(),
            'assigned_requests' => EmergencyRequest::whereIn('status', ['Assigned', 'In Progress'])->count(),
            'completed_requests' => EmergencyRequest::where('status', 'Completed')->count(),
            'critical_urgent' => EmergencyRequest::whereIn('urgency', ['Critical', 'High'])->count()
        ];

        // Get available shelters for assignment
        $shelters = Shelter::where('status', 'active')
            ->where('current_occupancy', '<', DB::raw('capacity'))
            ->get();

        return view('admin.requests.index', compact('requests', 'stats', 'shelters'));
    }

    /**
     * Show citizen request form
     */
    public function create()
    {
        return view('requests.create');
    }

    /**
     * Store a new citizen request
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'location' => 'required|string|max:255',
            'emergency_type' => 'required|string|in:Shelter,Medical,Food,Water,Rescue,Other',
            'description' => 'required|string|max:1000',
            'family_size' => 'nullable|integer|min:1|max:50',
            'special_needs' => 'nullable|string|max:500'
        ]);

        // Get current user or create a guest user entry
        $userId = Auth::id();
        if (!$userId) {
            // For guests, we'll use user_id = 1 (assuming there's a system user)
            // In production, you might want to create a guest user or handle this differently
            $userId = 1;
        }

        // Get coordinates from location (optional)
        $coordinates = $this->getCoordinatesFromLocation($request->input('location'));

        // Create the emergency request
        $emergencyRequest = EmergencyRequest::create([
            'user_id' => $userId,
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'] ?? null,
            'request_type' => $validatedData['emergency_type'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'latitude' => $coordinates['lat'] ?? null,
            'longitude' => $coordinates['lng'] ?? null,
            'people_count' => $validatedData['family_size'] ?? 1,
            'special_needs' => $validatedData['special_needs'] ?? null,
            'urgency' => $this->determineUrgency($validatedData['emergency_type']),
            'status' => 'Pending'
        ]);

        // Simulate admin availability check
        $adminOnline = $this->checkAdminAvailability();
        
        $status = 'Pending';
        $assignmentType = 'Manual';
        $message = 'Your emergency request has been submitted successfully. Request ID: #' . $emergencyRequest->id . '. An admin will review and assign assistance shortly.';
        
        if (!$adminOnline) {
            // Admin offline - try auto-assignment
            $nearestShelter = $this->autoAssignShelter($request->input('location'));
            if ($nearestShelter) {
                $emergencyRequest->update([
                    'status' => 'Assigned',
                    'assigned_at' => now()
                ]);
                $status = 'Auto-Assigned';
                $assignmentType = 'Auto';
                $message = 'Auto-assigned to ' . $nearestShelter['name'] . '. Please proceed immediately. Request ID: #' . $emergencyRequest->id;
            }
        }

        $citizenData = [
            'name' => $emergencyRequest->name,
            'phone' => $emergencyRequest->phone,
            'location' => $emergencyRequest->location,
            'emergency_type' => $emergencyRequest->request_type,
            'description' => $emergencyRequest->description,
            'family_size' => $emergencyRequest->people_count
        ];

        return view('requests.success', compact('emergencyRequest', 'status', 'message', 'citizenData'))->with('requestId', $emergencyRequest->id);
    }

    /**
     * Display a specific request
     */
    public function show($id)
    {
        // Get request from database
        $emergencyRequest = EmergencyRequest::with(['user', 'assignedBy'])->find($id);

        if (!$emergencyRequest) {
            abort(404, 'Request not found');
        }

        // Format data for view compatibility
        $request = [
            'id' => $emergencyRequest->id,
            'citizen_name' => $emergencyRequest->name,
            'phone' => $emergencyRequest->phone,
            'email' => $emergencyRequest->email,
            'location' => $emergencyRequest->location,
            'emergency_type' => $emergencyRequest->request_type,
            'description' => $emergencyRequest->description,
            'status' => $emergencyRequest->status,
            'urgency' => $emergencyRequest->urgency,
            'priority' => $emergencyRequest->urgency, // Map urgency to priority
            'people_count' => $emergencyRequest->people_count,
            'family_size' => $emergencyRequest->people_count, // Compatibility
            'special_needs' => $emergencyRequest->special_needs,
            'created_at' => $emergencyRequest->created_at->format('Y-m-d H:i:s'),
            'assigned_at' => $emergencyRequest->assigned_at ? $emergencyRequest->assigned_at->format('Y-m-d H:i:s') : null,
            'assigned_by_name' => $emergencyRequest->assignedBy ? $emergencyRequest->assignedBy->name : null,
            'assignment_type' => $emergencyRequest->assigned_at ? 'Manual' : null,
            'admin_notes' => $emergencyRequest->admin_notes,
            'assigned_shelter' => null, // TODO: Implement shelter assignment
            'shelter_id' => null
        ];

        return view('requests.show', compact('request'));
    }

    /**
     * Show assignment form for a request
     */
    public function assign($id)
    {
        $emergencyRequest = EmergencyRequest::findOrFail($id);
        
        // Get available shelters
        $shelters = Shelter::where('status', 'active')
            ->where('current_occupancy', '<', DB::raw('capacity'))
            ->get();

        return view('admin.requests.assign', compact('emergencyRequest', 'shelters'));
    }

    /**
     * Store shelter assignment for a request
     */
    public function storeAssignment(Request $request, $id)
    {
        $request->validate([
            'shelter_id' => 'required|exists:shelters,id',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $emergencyRequest = EmergencyRequest::findOrFail($id);
        $shelter = Shelter::findOrFail($request->shelter_id);

        // Check if shelter has capacity
        if ($shelter->current_occupancy >= $shelter->capacity) {
            return back()->with('error', 'Selected shelter is at full capacity.');
        }

        // Update the request
        $emergencyRequest->update([
            'status' => 'Assigned',
            'assigned_at' => now(),
            'assigned_by' => Auth::id() ?? 1, // Use current admin or fallback
            'admin_notes' => $request->admin_notes
        ]);

        // Update shelter occupancy
        $shelter->increment('current_occupancy', $emergencyRequest->people_count);

        // Create assignment record (if Assignment model exists)
        try {
            if (class_exists('\App\Models\Assignment')) {
                \App\Models\Assignment::create([
                    'request_id' => $emergencyRequest->id,
                    'shelter_id' => $shelter->id,
                    'assigned_by' => Auth::id() ?? 1,
                    'assigned_at' => now(),
                    'status' => 'Assigned'
                ]);
            }
        } catch (\Exception $e) {
            // Assignment model might not exist, continue without error
        }

        return redirect()->route('admin.requests')
            ->with('success', "Request #{$emergencyRequest->id} assigned to {$shelter->name} successfully!");
    }

    /**
     * Citizen dashboard to view their requests
     */
    public function citizenDashboard()
    {
        // Sample citizen's requests
        $myRequests = [
            [
                'id' => 1,
                'emergency_type' => 'Flood',
                'status' => 'Assigned',
                'assigned_shelter' => 'Dhaka Community Center',
                'created_at' => '2025-09-12 10:30:00'
            ],
            [
                'id' => 5,
                'emergency_type' => 'Building Safety',
                'status' => 'Completed',
                'assigned_shelter' => 'Dhaka Community Center',
                'created_at' => '2025-09-10 15:20:00'
            ]
        ];

        return view('requests.citizen-dashboard', compact('myRequests'));
    }

    /**
     * Determine urgency based on emergency type
     */
    private function determineUrgency($emergencyType)
    {
        $urgencyMap = [
            'Rescue' => 'Critical',
            'Medical' => 'High',
            'Shelter' => 'High',
            'Water' => 'Medium',
            'Food' => 'Medium',
            'Other' => 'Low'
        ];

        return $urgencyMap[$emergencyType] ?? 'Medium';
    }

    /**
     * Check if admin is currently online/available
     */
    private function checkAdminAvailability()
    {
        // Simulate admin availability (in real app, check last activity, online status, etc.)
        // For demo, randomly return true/false
        return rand(0, 1) === 1;
    }

    /**
     * Auto-assign nearest available shelter
     */
    private function autoAssignShelter($location)
    {
        // Simulate getting coordinates from location
        $coordinates = $this->getCoordinatesFromLocation($location);
        
        if (!$coordinates) {
            return null;
        }

        // Use ShelterController's findNearest method
        $shelterController = new \App\Http\Controllers\ShelterController();
        return $shelterController->findNearest($coordinates['lat'], $coordinates['lng']);
    }

    /**
     * Convert location string to coordinates (simulation)
     */
    private function getCoordinatesFromLocation($location)
    {
        // Sample location mapping
        $locationMap = [
            'Dhaka' => ['lat' => 23.8103, 'lng' => 90.4125],
            'Dhanmondi' => ['lat' => 23.7465, 'lng' => 90.3784],
            'Chittagong' => ['lat' => 22.3569, 'lng' => 91.7832],
            'Cox\'s Bazar' => ['lat' => 21.4272, 'lng' => 92.0058],
            'Sylhet' => ['lat' => 24.8949, 'lng' => 91.8687]
        ];

        // Simple matching
        foreach ($locationMap as $area => $coords) {
            if (stripos($location, $area) !== false) {
                return $coords;
            }
        }

        // Default to Dhaka center if not found
        return ['lat' => 23.8103, 'lng' => 90.4125];
    }
}
