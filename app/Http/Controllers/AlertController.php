<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{

    // displaying a listing of alerts

    public function index()
    {
        $alerts = Alert::with('creator')
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('alerts.index', compact('alerts'));
    }

    // display a specific alert 


    public function show($id)
    {
        $alert = Alert::with('creator')->findOrFail($id);

        return view('alerts.show', compact('alert'));
    }

    /** display home page with recent alerts */

    public function dashboard()
    {
        $recentAlerts = Alert::active()
            ->recent()
            ->limit(5)
            ->get();

        $stats = [
            'total_alerts' => Alert::count(),
            'active_alerts' => Alert::active()->count(),
            'high_severity' => Alert::where('severity', 'High')->count(),
            'medium_severity' => Alert::where('severity', 'Medium')->count()
        ];

        return view('dashboard', compact('recentAlerts', 'stats'));
    }

    /**
     * Admin-specific alerts management page
     */
    public function adminIndex()
    {
        $alerts = Alert::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_alerts' => Alert::count(),
            'active_alerts' => Alert::active()->count(),
            'expired_alerts' => Alert::where('status', 'Expired')->count(),
            'high_severity' => Alert::where('severity', 'High')->count(),
            'critical_severity' => Alert::where('severity', 'Critical')->count()
        ];

        return view('admin.alerts.index', compact('alerts', 'stats'));
    }

    /**
     * Show form for creating new alert
     */
    public function create()
    {
        return view('admin.alerts.create');
    }

    /**
     * Store a new alert
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Moderate,High,Critical',
            'type' => 'required|in:Flood,Earthquake,Cyclone,Fire,Health Emergency,Other',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'expires_at' => 'nullable|date|after:now'
        ]);

        $validated['created_by'] = Auth::id() ?? 1; // Default to admin user
        $validated['issued_at'] = now();
        $validated['status'] = 'Active';

        Alert::create($validated);

        return redirect()->route('admin.alerts')->with('success', 'Alert created successfully!');
    }

    /**
     * Show form for editing alert
     */
    public function edit($id)
    {
        $alert = Alert::findOrFail($id);
        return view('admin.alerts.edit', compact('alert'));
    }

    /**
     * Update alert
     */
    public function update(Request $request, $id)
    {
        $alert = Alert::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Moderate,High,Critical',
            'type' => 'required|in:Flood,Earthquake,Cyclone,Fire,Health Emergency,Other',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:Active,Resolved,Monitoring',
            'expires_at' => 'nullable|date'
        ]);

        $alert->update($validated);

        return redirect()->route('admin.alerts')->with('success', 'Alert updated successfully!');
    }

    /**
     * Delete alert
     */
    public function destroy($id)
    {
        $alert = Alert::findOrFail($id);
        $alert->delete();

        return redirect()->route('admin.alerts')->with('success', 'Alert deleted successfully!');
    }
}
