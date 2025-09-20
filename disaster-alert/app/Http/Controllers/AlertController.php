<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use App\Models\Shelter;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{

    /**
     * Display a listing of alerts (Public view)
     */
    public function index()
    {
        // Get active alerts from database ordered by severity and creation date
        $alerts = Alert::active()
            ->orderByRaw("FIELD(severity, 'Critical', 'High', 'Moderate', 'Low')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'title' => $alert->title,
                    'description' => $alert->description,
                    'severity' => $alert->severity,
                    'location' => $alert->location,
                    'type' => $alert->type,
                    'status' => $alert->status,
                    'created_at' => $alert->created_at->format('Y-m-d H:i:s'),
                    'expires_at' => $alert->expires_at ? $alert->expires_at->format('Y-m-d H:i:s') : null,
                    'issued_at' => $alert->issued_at ? $alert->issued_at->format('Y-m-d H:i:s') : null
                ];
            })->toArray();

        return view('alerts.index', compact('alerts'));
    }

    // display a specific alert 


    public function show($id)
    {
        // sample alert data


        $alerts = [
            1 => [
                'id' => 1,
                'title' => 'Flood Warning - Dhaka',
                'description' => 'Heavy rainfall expected in Dhaka metropolitan area. Water levels rising in major rivers. Citizens are advised to stay indoors and avoid unnecessary travel. Emergency shelters are being prepared.',
                'severity' => 'High',
                'location' => 'Dhaka, Bangladesh',
                'created_at' => '2025-09-10 10:00:00',
                'affected_areas' => ['Dhanmondi', 'Gulshan', 'Banani', 'Old Dhaka'],
                'emergency_contacts' => ['999', '01XXXXXXXXX']
            ],


            2 => [
                'id' => 2,
                'title' => 'Earthquake Alert - Chittagong',
                'description' => 'Minor earthquake of magnitude 4.2 detected in Chittagong region. No immediate structural damage reported. Citizens should remain calm and follow safety protocols.',
                'severity' => 'Medium',
                'location' => 'Chittagong, Bangladesh',
                'created_at' => '2025-09-10 08:30:00',
                'affected_areas' => ['Chittagong City', 'Fatikchari', 'Hathazari'],
                'emergency_contacts' => ['999', '01XXXXXXXXX']
            ],

            3 => [
                'id' => 3,
                'title' => 'Cyclone Watch - Cox\'s Bazar',
                'description' => 'Severe cyclone approaching Cox\'s Bazar coastal areas. Wind speed expected to reach 120 km/h. Immediate evacuation to designated shelters is recommended.',
                'severity' => 'High',
                'location' => 'Cox\'s Bazar, Bangladesh',
                'created_at' => '2025-09-10 06:15:00',
                'affected_areas' => ['Cox\'s Bazar Sadar', 'Teknaf', 'Ukhia', 'Ramu'],
                'emergency_contacts' => ['999', '01XXXXXXXXX']
            ]
        ];

        $alert = $alerts[$id] ?? null;

        if (!$alert) {
            abort(404, 'Alert not found');
        }

        return view('alerts.show', compact('alert'));
    }

    /** display home page with recent alerts */

    public function dashboard()
    {
        // sample data for dashboard

        $recentAlerts = [
            [
                'id' => 1,
                'title' => 'Flood Warning - Dhaka',
                'severity' => 'High',
                'created_at' => '2025-09-10 10:00:00'
            ],
            
            [
                'id' => 3,
                'title' => 'Cyclone Watch - Cox\'s Bazar',
                'severity' => 'High',
                'created_at' => '2025-09-10 06:15:00'
            ]
        ];

        $stats = [
            'total_alerts' => 3,
            'active_alerts' => 2,
            'high_severity' => 2,
            'medium_severity' => 1
        ];

        return view('dashboard', compact('recentAlerts', 'stats'));
    }

    // ADMIN-SPECIFIC METHODS FOR CRUD OPERATIONS

    /**
     * Admin view - Manage all alerts with create/edit/delete options
     */
    public function adminIndex()
    {
        // Sample alert data with admin controls
        $alerts = [
            [
                'id' => 1,
                'title' => 'Flood Warning - Dhaka',
                'description' => 'Heavy rainfall expected. Citizens advised to stay indoors.',
                'instructions' => 'Move to higher ground, avoid walking in flood water, keep emergency supplies ready.',
                'severity' => 'High',
                'type' => 'Flood',
                'location' => 'Dhaka, Bangladesh',
                'status' => 'Active',
                'created_at' => '2025-09-10 10:00:00',
                'expires_at' => '2025-09-11 10:00:00'
            ],
            [
                'id' => 2,
                'title' => 'Earthquake Alert - Chittagong',
                'description' => 'Minor earthquake detected. No immediate danger.',
                'instructions' => 'Stay calm, check for damage, be prepared for aftershocks.',
                'severity' => 'Medium',
                'type' => 'Earthquake',
                'location' => 'Chittagong, Bangladesh',
                'status' => 'Active',
                'created_at' => '2025-09-10 08:30:00',
                'expires_at' => '2025-09-10 20:30:00'
            ],
            [
                'id' => 3,
                'title' => 'Cyclone Watch - Cox\'s Bazar',
                'description' => 'Cyclone approaching coastal areas. Evacuation recommended.',
                'instructions' => 'Evacuate immediately to designated shelters, secure loose objects, stock emergency supplies.',
                'severity' => 'High',
                'type' => 'Cyclone',
                'location' => 'Cox\'s Bazar, Bangladesh',
                'status' => 'Draft',
                'created_at' => '2025-09-10 06:15:00',
                'expires_at' => '2025-09-12 06:15:00'
            ]
        ];

        $stats = [
            'total_alerts' => 3,
            'active_alerts' => 2,
            'draft_alerts' => 1,
            'critical_alerts' => 2
        ];

        return view('admin.alerts.index', compact('alerts', 'stats'));
    }

    /**
     * Show form to create new alert (Admin only)
     */
    public function create()
    {
        return view('admin.alerts.create');
    }

    /**
     * Store new alert (Admin only)
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Moderate,High,Critical',
            'type' => 'required|in:Flood,Earthquake,Cyclone,Fire,Health Emergency,Other',
            'location' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'instructions' => 'nullable|string'
        ]);

        // Create new alert in database
        Alert::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'type' => $validated['type'],
            'location' => $validated['location'],
            'status' => 'Active',
            'issued_at' => now(),
            'expires_at' => $validated['expires_at'] ? $validated['expires_at'] : null,
            'created_by' => Auth::id() ?? 1 // Use authenticated admin or fallback
        ]);

        return redirect()->route('admin.alerts')
            ->with('success', 'Alert created successfully and is now visible to citizens!');
    }

    /**
     * Show form to edit alert (Admin only)
     */
    public function edit($id)
    {
        // Sample alert data
        $alert = [
            'id' => $id,
            'title' => 'Flood Warning - Dhaka',
            'description' => 'Heavy rainfall expected. Citizens advised to stay indoors.',
            'severity' => 'High',
            'type' => 'Flood',
            'location' => 'Dhaka, Bangladesh',
            'status' => 'Active',
            'expires_at' => '2025-09-11 10:00:00'
        ];

        return view('admin.alerts.edit', compact('alert'));
    }

    /**
     * Update alert (Admin only)
     */
    public function update(Request $request, $id)
    {
        // Validate and update logic here
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Moderate,High,Critical',
            'type' => 'required|in:Flood,Earthquake,Cyclone,Fire,Health Emergency,Other',
            'location' => 'required|string|max:255',
            'status' => 'required|in:Active,Resolved,Monitoring',
            'expires_at' => 'nullable|date',
            'instructions' => 'nullable|string'
        ]);

        // Find and update alert in database
        $alert = Alert::findOrFail($id);
        $alert->update($validated);

        return redirect()->route('admin.alerts')
            ->with('success', 'Alert updated successfully! Changes are now visible to citizens.');
    }

    /**
     * Delete alert (Admin only)
     */
    public function destroy($id)
    {
        // Find and delete alert from database
        $alert = Alert::findOrFail($id);
        $alert->delete();

        return redirect()->route('admin.alerts')
            ->with('success', 'Alert deleted successfully! It is no longer visible to citizens.');
    }
}
