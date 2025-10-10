<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelter;
use Illuminate\Support\Facades\DB;

class ShelterController extends Controller
{
    /**
     * Display a listing of shelters
     */
    public function index()
    {
        $shelters = Shelter::where('status', 'Active')
            ->orderBy('name')
            ->get()
            ->map(function($shelter) {
                return [
                    'id' => $shelter->id,
                    'name' => $shelter->name,
                    'location' => $shelter->city . ', ' . $shelter->state,
                    'address' => $shelter->address,
                    'capacity' => $shelter->capacity,
                    'occupied' => $shelter->current_occupancy,
                    'available' => $shelter->capacity - $shelter->current_occupancy,
                    'status' => $shelter->current_occupancy >= $shelter->capacity ? 'Full' : 
                               ($shelter->current_occupancy >= $shelter->capacity * 0.9 ? 'Nearly Full' : 'Available'),
                    'contact' => $shelter->contact_phone,
                    'facilities' => $shelter->facilities ?? [],
                    'coordinates' => ['lat' => $shelter->latitude, 'lng' => $shelter->longitude]
                ];
            });

        // Calculate statistics
        $stats = [
            'total_shelters' => $shelters->count(),
            'available_shelters' => $shelters->where('status', 'Available')->count(),
            'total_capacity' => $shelters->sum('capacity'),
            'total_occupied' => $shelters->sum('occupied'),
            'total_available' => $shelters->sum('available')
        ];

        return view('shelters.index', compact('shelters', 'stats'));
    }

    /**
     * Display a specific shelter
     */
    public function show($id)
    {
        $shelterModel = Shelter::findOrFail($id);
        
        $shelter = [
            'id' => $shelterModel->id,
            'name' => $shelterModel->name,
            'location' => $shelterModel->city . ', ' . $shelterModel->state,
            'address' => $shelterModel->address,
            'capacity' => $shelterModel->capacity,
            'occupied' => $shelterModel->current_occupancy,
            'available' => $shelterModel->capacity - $shelterModel->current_occupancy,
            'status' => $shelterModel->current_occupancy >= $shelterModel->capacity ? 'Full' : 
                       ($shelterModel->current_occupancy >= $shelterModel->capacity * 0.9 ? 'Nearly Full' : 'Available'),
            'contact' => $shelterModel->contact_phone,
            'manager' => 'Shelter Manager', // Can be added to model later
            'facilities' => $shelterModel->facilities ?? [],
            'coordinates' => ['lat' => $shelterModel->latitude, 'lng' => $shelterModel->longitude],
            'description' => $shelterModel->description,
            'safety_measures' => ['Fire Safety System', 'Emergency Exits', 'First Aid Station', '24/7 Security'],
            'last_updated' => $shelterModel->updated_at->format('Y-m-d H:i:s')
        ];

        return view('shelters.show', compact('shelter'));
    }

    /**
     * Find nearest available shelter (for auto-assignment)
     */
    public function findNearest($latitude, $longitude)
    {
        $nearestShelter = Shelter::select('id', 'name', 'capacity', 'current_occupancy', 'latitude', 'longitude')
            ->where('status', 'Active')
            ->whereRaw('(capacity - current_occupancy) > 0')
            ->selectRaw('*, ( 
                6371 * acos( 
                    cos( radians(?) ) * 
                    cos( radians( latitude ) ) * 
                    cos( radians( longitude ) - radians(?) ) + 
                    sin( radians(?) ) * 
                    sin( radians( latitude ) ) 
                ) 
            ) AS distance', [$latitude, $longitude, $latitude])
            ->orderBy('distance')
            ->first();

        if ($nearestShelter) {
            return [
                'id' => $nearestShelter->id,
                'name' => $nearestShelter->name,
                'available' => $nearestShelter->capacity - $nearestShelter->current_occupancy,
                'lat' => $nearestShelter->latitude,
                'lng' => $nearestShelter->longitude
            ];
        }

        return null;
    }

    /**
     * Admin shelter management dashboard
     */
    public function manage()
    {
        $shelters = Shelter::orderBy('name')->get();
        
        $stats = [
            'total_shelters' => $shelters->count(),
            'available_shelters' => $shelters->where('status', 'Active')
                ->filter(fn($s) => $s->current_occupancy < $s->capacity)->count(),
            'total_capacity' => $shelters->sum('capacity'),
            'total_occupied' => $shelters->sum('current_occupancy'),
            'total_available' => $shelters->sum(fn($s) => $s->capacity - $s->current_occupancy)
        ];

        return view('shelters.manage', compact('shelters', 'stats'));
    }

    /**
     * Admin-specific shelter management page
     */
    public function adminIndex()
    {
        $shelters = Shelter::orderBy('name')->paginate(10);
        
        $stats = [
            'total_shelters' => Shelter::count(),
            'active_shelters' => Shelter::where('status', 'Active')->count(),
            'full_shelters' => Shelter::whereRaw('current_occupancy >= capacity')->count(),
            'available_shelters' => Shelter::where('status', 'Active')
                ->whereRaw('current_occupancy < capacity')->count(),
            'total_capacity' => Shelter::sum('capacity'),
            'total_occupied' => Shelter::sum('current_occupancy')
        ];

        return view('admin.shelters.index', compact('shelters', 'stats'));
    }

    /**
     * Show form for creating new shelter
     */
    public function create()
    {
        return view('admin.shelters.create');
    }

    /**
     * Store a new shelter
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string'
        ]);

        $validated['current_occupancy'] = 0;
        $validated['status'] = 'Active';

        Shelter::create($validated);

        return redirect()->route('admin.shelters')->with('success', 'Shelter created successfully!');
    }

    /**
     * Show form for editing shelter
     */
    public function edit($id)
    {
        $shelter = Shelter::findOrFail($id);
        return view('admin.shelters.edit', compact('shelter'));
    }

    /**
     * Update shelter
     */
    public function update(Request $request, $id)
    {
        $shelter = Shelter::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'current_occupancy' => 'required|integer|min:0',
            'status' => 'required|in:Active,Maintenance,Closed',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string'
        ]);

        $shelter->update($validated);

        return redirect()->route('admin.shelters')->with('success', 'Shelter updated successfully!');
    }

    /**
     * Delete shelter
     */
    public function destroy($id)
    {
        $shelter = Shelter::findOrFail($id);
        
        // Check if shelter has current assignments
        $hasAssignments = DB::table('assignments')
            ->where('shelter_id', $id)
            ->whereIn('status', ['Assigned', 'Checked In'])
            ->exists();

        if ($hasAssignments) {
            return redirect()->route('admin.shelters')
                ->with('error', 'Cannot delete shelter with active assignments. Please reassign people first.');
        }

        $shelter->delete();

        return redirect()->route('admin.shelters')->with('success', 'Shelter deleted successfully!');
    }
}
