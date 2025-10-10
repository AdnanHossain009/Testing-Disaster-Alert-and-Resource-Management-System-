<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;

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
}
