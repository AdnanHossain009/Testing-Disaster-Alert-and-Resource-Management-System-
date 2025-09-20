<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelter;

class ShelterController extends Controller
{
    /**
     * Display a listing of shelters (Public view)
     */
    public function index()
    {
        // Get active shelters from database
        $shelters = Shelter::active()
            ->orderBy('name')
            ->get()
            ->map(function ($shelter) {
                return [
                    'id' => $shelter->id,
                    'name' => $shelter->name,
                    'location' => $shelter->city,
                    'address' => $shelter->address,
                    'capacity' => $shelter->capacity,
                    'occupied' => $shelter->current_occupancy,
                    'available' => $shelter->available_capacity,
                    'status' => $shelter->availability_status,
                    'contact' => $shelter->contact_phone,
                    'facilities' => is_array($shelter->facilities) ? $shelter->facilities : [],
                    'coordinates' => ['lat' => $shelter->latitude ?? 0, 'lng' => $shelter->longitude ?? 0],
                    'special_notes' => $shelter->special_notes
                ];
            })->toArray();

        // Calculate statistics
        $stats = [
            'total_shelters' => count($shelters),
            'available_shelters' => count(array_filter($shelters, fn($s) => $s['status'] === 'Available')),
            'total_capacity' => array_sum(array_column($shelters, 'capacity')),
            'total_occupied' => array_sum(array_column($shelters, 'occupied')),
            'total_available' => array_sum(array_column($shelters, 'available'))
        ];

        return view('shelters.index', compact('shelters', 'stats'));
    }

    /**
     * Display admin shelter management page
     */
    public function adminIndex()
    {
        // Get all shelters from database for admin management
        $shelters = Shelter::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($shelter) {
                return [
                    'id' => $shelter->id,
                    'name' => $shelter->name,
                    'location' => $shelter->city,
                    'address' => $shelter->address,
                    'capacity' => $shelter->capacity,
                    'occupied' => $shelter->current_occupancy,
                    'available' => $shelter->available_capacity,
                    'status' => $shelter->availability_status,
                    'contact' => $shelter->contact_phone,
                    'facilities' => is_array($shelter->facilities) ? $shelter->facilities : [],
                    'coordinates' => ['lat' => $shelter->latitude ?? 0, 'lng' => $shelter->longitude ?? 0],
                    'special_notes' => $shelter->special_notes,
                    'created_at' => $shelter->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $shelter->updated_at->format('Y-m-d H:i:s')
                ];
            })->toArray();

        // Calculate statistics for admin dashboard
        $stats = [
            'total_shelters' => count($shelters),
            'available_shelters' => count(array_filter($shelters, fn($s) => $s['status'] === 'Available')),
            'total_capacity' => array_sum(array_column($shelters, 'capacity')),
            'total_occupied' => array_sum(array_column($shelters, 'occupied')),
            'total_available' => array_sum(array_column($shelters, 'available'))
        ];

        return view('admin.shelters.index', compact('shelters', 'stats'));
    }

    /**
     * Display a specific shelter
     */
    public function show($id)
    {
        // Sample shelter data
        $shelters = [
            1 => [
                'id' => 1,
                'name' => 'Dhaka Community Center',
                'location' => 'Dhanmondi, Dhaka',
                'address' => '15/A Dhanmondi Road, Dhaka-1205',
                'capacity' => 200,
                'occupied' => 45,
                'available' => 155,
                'status' => 'Available',
                'contact' => '+880-1XXXXXXXXX',
                'manager' => 'Mr. Abdul Rahman',
                'facilities' => ['Food Service', 'Medical Aid', 'Sleeping Area', 'Children Play Area', 'Restrooms', 'Security'],
                'coordinates' => ['lat' => 23.7465, 'lng' => 90.3784],
                'description' => 'Large community center with modern facilities. Equipped with backup power and water supply. Medical team available 24/7.',
                'safety_measures' => ['Fire Safety System', 'Emergency Exits', 'First Aid Station', '24/7 Security'],
                'last_updated' => '2025-09-12 14:30:00'
            ],
            2 => [
                'id' => 2,
                'name' => 'Cox\'s Bazar Relief Center',
                'location' => 'Cox\'s Bazar Sadar',
                'address' => 'Beach Road, Cox\'s Bazar-4700',
                'capacity' => 150,
                'occupied' => 130,
                'available' => 20,
                'status' => 'Nearly Full',
                'contact' => '+880-1XXXXXXXXX',
                'manager' => 'Ms. Fatima Khatun',
                'facilities' => ['Food Service', 'Medical Aid', 'Sleeping Area', 'Restrooms'],
                'coordinates' => ['lat' => 21.4272, 'lng' => 92.0058],
                'description' => 'Coastal relief center designed for cyclone and tsunami emergencies. Close to evacuation routes.',
                'safety_measures' => ['Tsunami Warning System', 'Emergency Exits', 'First Aid Station'],
                'last_updated' => '2025-09-12 13:45:00'
            ],
            3 => [
                'id' => 3,
                'name' => 'Chittagong Sports Complex',
                'location' => 'Chittagong City',
                'address' => 'GEC Circle, Chittagong-4000',
                'capacity' => 300,
                'occupied' => 0,
                'available' => 300,
                'status' => 'Available',
                'contact' => '+880-1XXXXXXXXX',
                'manager' => 'Mr. Karim Ahmed',
                'facilities' => ['Food Service', 'Medical Aid', 'Sleeping Area', 'Sports Facilities', 'Large Halls'],
                'coordinates' => ['lat' => 22.3569, 'lng' => 91.7832],
                'description' => 'Large sports complex with multiple halls and outdoor areas. Suitable for large-scale evacuations.',
                'safety_measures' => ['Fire Safety System', 'Multiple Emergency Exits', 'Medical Facility', 'Security Team'],
                'last_updated' => '2025-09-12 15:00:00'
            ]
        ];

        $shelter = $shelters[$id] ?? null;

        if (!$shelter) {
            abort(404, 'Shelter not found');
        }

        return view('shelters.show', compact('shelter'));
    }

    /**
     * Find nearest available shelter (for auto-assignment)
     */
    public function findNearest($latitude, $longitude)
    {
        // Sample logic for finding nearest shelter
        $shelters = [
            ['id' => 1, 'name' => 'Dhaka Community Center', 'available' => 155, 'lat' => 23.7465, 'lng' => 90.3784],
            ['id' => 2, 'name' => 'Cox\'s Bazar Relief Center', 'available' => 20, 'lat' => 21.4272, 'lng' => 92.0058],
            ['id' => 3, 'name' => 'Chittagong Sports Complex', 'available' => 300, 'lat' => 22.3569, 'lng' => 91.7832]
        ];

        // Calculate distances and find nearest available shelter
        $nearestShelter = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($shelters as $shelter) {
            if ($shelter['available'] > 0) {
                // Simple distance calculation (in real app, use proper geo calculation)
                $distance = sqrt(pow($latitude - $shelter['lat'], 2) + pow($longitude - $shelter['lng'], 2));
                
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestShelter = $shelter;
                }
            }
        }

        return $nearestShelter;
    }

    /**
     * Admin shelter management dashboard
     */
    public function manage()
    {
        // For admin to manage shelters
        $shelters = $this->index()->getData()['shelters'];
        $stats = $this->index()->getData()['stats'];

        return view('shelters.manage', compact('shelters', 'stats'));
    }

    /**
     * Show the form for creating a new shelter
     */
    public function create()
    {
        return view('admin.shelters.create');
    }

    /**
     * Store a newly created shelter in storage
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'facilities' => 'nullable|array',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'special_notes' => 'nullable|string|max:1000'
        ]);

        // Set default values
        $validatedData['current_occupancy'] = 0;
        $validatedData['is_active'] = true;

        Shelter::create($validatedData);

        return redirect()->route('admin.shelters')->with('success', 'Shelter created successfully!');
    }

    /**
     * Show the form for editing the specified shelter
     */
    public function edit($id)
    {
        $shelter = Shelter::findOrFail($id);
        return view('admin.shelters.edit', compact('shelter'));
    }

    /**
     * Update the specified shelter in storage
     */
    public function update(Request $request, $id)
    {
        $shelter = Shelter::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'facilities' => 'nullable|array',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'special_notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $shelter->update($validatedData);

        return redirect()->route('admin.shelters')->with('success', 'Shelter updated successfully!');
    }

    /**
     * Remove the specified shelter from storage
     */
    public function destroy($id)
    {
        $shelter = Shelter::findOrFail($id);
        $shelter->delete();

        return redirect()->route('admin.shelters')->with('success', 'Shelter deleted successfully!');
    }
}
