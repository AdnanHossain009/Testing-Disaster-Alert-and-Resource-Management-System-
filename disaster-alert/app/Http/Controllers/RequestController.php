<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Display all citizen requests (Admin view)
     */
    public function index()
    {
        // Sample citizen requests data
        $requests = [
            [
                'id' => 1,
                'citizen_name' => 'John Rahman',
                'phone' => '+880-1XXXXXXXXX',
                'location' => 'Dhanmondi, Dhaka',
                'emergency_type' => 'Flood',
                'description' => 'Water level rising rapidly in our area. Need immediate evacuation.',
                'status' => 'Assigned',
                'assigned_shelter' => 'Dhaka Community Center',
                'shelter_id' => 1,
                'priority' => 'High',
                'created_at' => '2025-09-12 10:30:00',
                'assigned_at' => '2025-09-12 10:35:00',
                'assignment_type' => 'Manual'
            ],
            [
                'id' => 2,
                'citizen_name' => 'Fatima Khatun',
                'phone' => '+880-1XXXXXXXXX',
                'location' => 'Old Dhaka',
                'emergency_type' => 'Building Collapse Risk',
                'description' => 'Our building has cracks after earthquake. Family of 4 needs shelter.',
                'status' => 'Pending',
                'assigned_shelter' => null,
                'shelter_id' => null,
                'priority' => 'High',
                'created_at' => '2025-09-12 11:45:00',
                'assigned_at' => null,
                'assignment_type' => null
            ],
            [
                'id' => 3,
                'citizen_name' => 'Ahmed Hassan',
                'phone' => '+880-1XXXXXXXXX',
                'location' => 'Chittagong',
                'emergency_type' => 'Cyclone',
                'description' => 'Strong winds approaching. Elderly parents need safe shelter.',
                'status' => 'Auto-Assigned',
                'assigned_shelter' => 'Chittagong Sports Complex',
                'shelter_id' => 3,
                'priority' => 'Medium',
                'created_at' => '2025-09-12 09:15:00',
                'assigned_at' => '2025-09-12 09:16:00',
                'assignment_type' => 'Auto'
            ],
            [
                'id' => 4,
                'citizen_name' => 'Rashida Begum',
                'phone' => '+880-1XXXXXXXXX',
                'location' => 'Cox\'s Bazar',
                'emergency_type' => 'Tsunami Warning',
                'description' => 'Tsunami alert issued. Need immediate evacuation for 6 family members.',
                'status' => 'Completed',
                'assigned_shelter' => 'Cox\'s Bazar Relief Center',
                'shelter_id' => 2,
                'priority' => 'Critical',
                'created_at' => '2025-09-12 06:00:00',
                'assigned_at' => '2025-09-12 06:02:00',
                'assignment_type' => 'Manual'
            ]
        ];

        // Calculate statistics
        $stats = [
            'total_requests' => count($requests),
            'pending_requests' => count(array_filter($requests, fn($r) => $r['status'] === 'Pending')),
            'assigned_requests' => count(array_filter($requests, fn($r) => in_array($r['status'], ['Assigned', 'Auto-Assigned']))),
            'completed_requests' => count(array_filter($requests, fn($r) => $r['status'] === 'Completed')),
            'auto_assignments' => count(array_filter($requests, fn($r) => $r['assignment_type'] === 'Auto'))
        ];

        return view('requests.index', compact('requests', 'stats'));
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
        // In real app, this would save to database
        // For now, simulate the auto-assignment process
        
        $citizenData = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'location' => $request->input('location'),
            'emergency_type' => $request->input('emergency_type'),
            'description' => $request->input('description'),
            'family_size' => $request->input('family_size', 1)
        ];

        // Simulate admin availability check
        $adminOnline = $this->checkAdminAvailability();
        
        if ($adminOnline) {
            // Admin is online - manual assignment will be done
            $status = 'Pending';
            $assignmentType = 'Manual';
            $message = 'Your request has been submitted. An admin will assign you a shelter shortly.';
        } else {
            // Admin offline - auto-assign nearest shelter
            $nearestShelter = $this->autoAssignShelter($request->input('location'));
            $status = $nearestShelter ? 'Auto-Assigned' : 'Pending';
            $assignmentType = 'Auto';
            $message = $nearestShelter 
                ? 'Auto-assigned to ' . $nearestShelter['name'] . '. Please proceed immediately.'
                : 'No available shelters found. Your request is pending manual review.';
        }

        // Simulate saving request
        $requestId = rand(100, 999);

        return view('requests.success', compact('requestId', 'status', 'message', 'citizenData'));
    }

    /**
     * Display a specific request
     */
    public function show($id)
    {
        // Sample request data
        $requests = [
            1 => [
                'id' => 1,
                'citizen_name' => 'John Rahman',
                'phone' => '+880-1XXXXXXXXX',
                'location' => 'Dhanmondi, Dhaka',
                'emergency_type' => 'Flood',
                'description' => 'Water level rising rapidly in our area. Need immediate evacuation for family of 3.',
                'status' => 'Assigned',
                'assigned_shelter' => 'Dhaka Community Center',
                'shelter_id' => 1,
                'priority' => 'High',
                'family_size' => 3,
                'created_at' => '2025-09-12 10:30:00',
                'assigned_at' => '2025-09-12 10:35:00',
                'assignment_type' => 'Manual',
                'admin_notes' => 'Verified emergency situation. Family safely relocated.'
            ]
        ];

        $request = $requests[$id] ?? null;

        if (!$request) {
            abort(404, 'Request not found');
        }

        return view('requests.show', compact('request'));
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
