<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpRequest;
use App\Models\Shelter;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    /**
     * Display all citizen requests (Admin view)
     */
    public function index()
    {
        $helpRequests = HelpRequest::with(['user', 'assignment.shelter'])
            ->orderBy('created_at', 'desc')
            ->get();

        $requests = $helpRequests->map(function($req) {
            return [
                'id' => $req->id,
                'citizen_name' => $req->name,
                'phone' => $req->phone,
                'location' => $req->location,
                'emergency_type' => $req->request_type,
                'description' => $req->description,
                'status' => $req->status,
                'assigned_shelter' => $req->assignment ? $req->assignment->shelter->name : null,
                'shelter_id' => $req->assignment ? $req->assignment->shelter_id : null,
                'priority' => $req->urgency,
                'created_at' => $req->created_at->format('Y-m-d H:i:s'),
                'assigned_at' => $req->assigned_at ? $req->assigned_at->format('Y-m-d H:i:s') : null,
                'assignment_type' => $req->assignment ? 'Manual' : null
            ];
        });

        // Calculate statistics
        $stats = [
            'total_requests' => $helpRequests->count(),
            'pending_requests' => $helpRequests->where('status', 'Pending')->count(),
            'assigned_requests' => $helpRequests->whereIn('status', ['Assigned', 'In Progress'])->count(),
            'completed_requests' => $helpRequests->where('status', 'Completed')->count(),
            'auto_assignments' => Assignment::count()
        ];

        return view('requests.index', compact('requests', 'stats'));
    }

    /**
     * Admin-specific requests management page
     */
    public function adminIndex()
    {
        $helpRequests = HelpRequest::with(['user', 'assignment.shelter'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $requests = $helpRequests->map(function($req) {
            return [
                'id' => $req->id,
                'citizen_name' => $req->name,
                'phone' => $req->phone,
                'location' => $req->location,
                'emergency_type' => $req->request_type,
                'description' => $req->description,
                'status' => $req->status,
                'assigned_shelter' => $req->assignment ? $req->assignment->shelter->name : null,
                'shelter_id' => $req->assignment ? $req->assignment->shelter_id : null,
                'priority' => $req->urgency,
                'created_at' => $req->created_at->format('Y-m-d H:i:s'),
                'assigned_at' => $req->assigned_at && $req->assigned_at instanceof \Carbon\Carbon ? $req->assigned_at->format('Y-m-d H:i:s') : $req->assigned_at,
                'assignment_type' => $req->assignment ? 'Manual' : null
            ];
        });

        // Calculate statistics for admin
        $stats = [
            'total_requests' => HelpRequest::count(),
            'pending_requests' => HelpRequest::where('status', 'Pending')->count(),
            'assigned_requests' => HelpRequest::whereIn('status', ['Assigned', 'In Progress'])->count(),
            'completed_requests' => HelpRequest::where('status', 'Completed')->count(),
            'critical_requests' => HelpRequest::where('urgency', 'Critical')->count(),
            'high_requests' => HelpRequest::where('urgency', 'High')->count()
        ];

        return view('admin.requests.index', compact('requests', 'stats', 'helpRequests'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'location' => 'required|string',
            'request_type' => 'required|in:Shelter,Medical,Food,Water,Rescue,Other',
            'description' => 'required|string',
            'people_count' => 'nullable|integer|min:1',
            'special_needs' => 'nullable|string'
        ]);

        $validated['user_id'] = Auth::id() ?? User::where('email', 'guest@emergency.system')->first()->id; // Use guest user for anonymous requests
        $validated['urgency'] = 'Medium'; // Default urgency
        $validated['status'] = 'Pending';

        // Get coordinates from location (simplified)
        $coords = $this->getCoordinatesFromLocation($validated['location']);
        $validated['latitude'] = $coords['lat'];
        $validated['longitude'] = $coords['lng'];

        $helpRequest = HelpRequest::create($validated);

        // Simulate admin availability check
        $adminOnline = $this->checkAdminAvailability();
        
        if (!$adminOnline) {
            // Auto-assign nearest shelter
            $nearestShelter = $this->autoAssignShelter($helpRequest);
            $status = $nearestShelter ? 'Auto-Assigned' : 'Pending';
            $message = $nearestShelter 
                ? 'Auto-assigned to ' . $nearestShelter->name . '. Please proceed immediately.'
                : 'No available shelters found. Your request is pending manual review.';
        } else {
            $status = 'Pending';
            $message = 'Your request has been submitted. An admin will assign you a shelter shortly.';
        }

        $citizenData = $validated;
        $requestId = $helpRequest->id;

        return view('requests.success', compact('requestId', 'status', 'message', 'citizenData'));
    }

    /**
     * Display a specific request
     */
    public function show($id)
    {
        $helpRequest = HelpRequest::with(['user'])->findOrFail($id);

        // Try to get assignment separately to avoid relationship issues
        $assignment = Assignment::where('request_id', $id)->with('shelter')->first();

        $request = [
            'id' => $helpRequest->id,
            'citizen_name' => $helpRequest->name,
            'phone' => $helpRequest->phone,
            'location' => $helpRequest->location,
            'emergency_type' => $helpRequest->request_type,
            'description' => $helpRequest->description,
            'status' => $helpRequest->status,
            'assigned_shelter' => $assignment && $assignment->shelter ? $assignment->shelter->name : null,
            'shelter_id' => $assignment ? $assignment->shelter_id : null,
            'priority' => $helpRequest->urgency ?? 'Medium',
            'family_size' => $helpRequest->people_count ?? 1,
            'created_at' => $helpRequest->created_at->format('Y-m-d H:i:s'),
            'assigned_at' => $assignment && $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i:s') : null,
            'assignment_type' => $assignment ? 'Manual' : null,
            'admin_notes' => $helpRequest->admin_notes
        ];

        return view('requests.show', compact('request'));
    }

    /**
     * Citizen dashboard to view their requests
     */
    public function citizenDashboard()
    {
        $userId = Auth::id() ?? User::where('role', 'citizen')->first()->id;
        
        $helpRequests = HelpRequest::with('assignment.shelter')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $myRequests = $helpRequests->map(function($req) {
            return [
                'id' => $req->id,
                'emergency_type' => $req->request_type,
                'status' => $req->status,
                'assigned_shelter' => $req->assignment ? $req->assignment->shelter->name : null,
                'created_at' => $req->created_at->format('Y-m-d H:i:s')
            ];
        });

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
    private function autoAssignShelter($helpRequest)
    {
        if (!$helpRequest->latitude || !$helpRequest->longitude) {
            return null;
        }

        $nearestShelter = Shelter::select('*')
            ->where('status', 'Active')
            ->whereRaw('(capacity - current_occupancy) > 0')
            ->selectRaw('( 
                6371 * acos( 
                    cos( radians(?) ) * 
                    cos( radians( latitude ) ) * 
                    cos( radians( longitude ) - radians(?) ) + 
                    sin( radians(?) ) * 
                    sin( radians( latitude ) ) 
                ) 
            ) AS distance', [$helpRequest->latitude, $helpRequest->longitude, $helpRequest->latitude])
            ->orderBy('distance')
            ->first();

        if ($nearestShelter) {
            // Create assignment
            Assignment::create([
                'request_id' => $helpRequest->id,
                'shelter_id' => $nearestShelter->id,
                'assigned_by' => 1, // System/Admin user
                'assigned_at' => now(),
                'status' => 'Assigned',
                'notes' => 'Auto-assigned to nearest available shelter'
            ]);

            // Update request status
            $helpRequest->update([
                'status' => 'Assigned',
                'assigned_at' => now(),
                'assigned_by' => 1
            ]);

            return $nearestShelter;
        }

        return null;
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
