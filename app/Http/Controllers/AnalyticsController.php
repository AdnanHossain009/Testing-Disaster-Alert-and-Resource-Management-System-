<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use App\Models\Shelter;
use App\Models\HelpRequest;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        $data = $this->getAnalyticsData();
        
        return view('admin.analytics-reports', $data);
    }

    /**
     * Get analytics data
     */
    private function getAnalyticsData()
    {
        // Alerts Analytics
        $alertsData = [
            'total' => Alert::count(),
            'active' => Alert::where('status', 'Active')->count(),
            'resolved' => Alert::where('status', 'Resolved')->count(),
            'by_severity' => [
                'Critical' => Alert::where('severity', 'Critical')->count(),
                'High' => Alert::where('severity', 'High')->count(),
                'Moderate' => Alert::where('severity', 'Moderate')->count(),
                'Low' => Alert::where('severity', 'Low')->count(),
            ],
            'by_type' => [
                'Flood' => Alert::where('type', 'Flood')->count(),
                'Earthquake' => Alert::where('type', 'Earthquake')->count(),
                'Cyclone' => Alert::where('type', 'Cyclone')->count(),
                'Fire' => Alert::where('type', 'Fire')->count(),
                'Health Emergency' => Alert::where('type', 'Health Emergency')->count(),
                'Other' => Alert::where('type', 'Other')->count(),
            ],
            'recent' => Alert::orderBy('created_at', 'desc')->limit(5)->get()
        ];

        // Shelters Analytics
        $shelters = Shelter::all();
        $totalCapacity = $shelters->sum('capacity');
        $totalOccupied = $shelters->sum('current_occupancy');
        $availableSpace = $totalCapacity - $totalOccupied;
        
        $sheltersData = [
            'total' => $shelters->count(),
            'active' => Shelter::where('status', 'Active')->count(),
            'full' => Shelter::where('status', 'Full')->count(),
            'inactive' => Shelter::where('status', 'Inactive')->count(),
            'total_capacity' => $totalCapacity,
            'total_occupied' => $totalOccupied,
            'available_space' => $availableSpace,
            'occupancy_rate' => $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 2) : 0,
            'by_status' => [
                'Active' => Shelter::where('status', 'Active')->count(),
                'Full' => Shelter::where('status', 'Full')->count(),
                'Inactive' => Shelter::where('status', 'Inactive')->count(),
                'Maintenance' => Shelter::where('status', 'Maintenance')->count(),
            ],
            'list' => $shelters
        ];

        // Requests Analytics
        $requestsData = [
            'total' => HelpRequest::count(),
            'pending' => HelpRequest::where('status', 'Pending')->count(),
            'assigned' => HelpRequest::where('status', 'Assigned')->count(),
            'in_progress' => HelpRequest::where('status', 'In Progress')->count(),
            'completed' => HelpRequest::where('status', 'Completed')->count(),
            'cancelled' => HelpRequest::where('status', 'Cancelled')->count(),
            'by_type' => [
                'Shelter' => HelpRequest::where('request_type', 'Shelter')->count(),
                'Medical' => HelpRequest::where('request_type', 'Medical')->count(),
                'Food' => HelpRequest::where('request_type', 'Food')->count(),
                'Water' => HelpRequest::where('request_type', 'Water')->count(),
                'Rescue' => HelpRequest::where('request_type', 'Rescue')->count(),
                'Other' => HelpRequest::where('request_type', 'Other')->count(),
            ],
            'by_urgency' => [
                'Critical' => HelpRequest::where('urgency', 'Critical')->count(),
                'High' => HelpRequest::where('urgency', 'High')->count(),
                'Medium' => HelpRequest::where('urgency', 'Medium')->count(),
                'Low' => HelpRequest::where('urgency', 'Low')->count(),
            ],
            'total_people' => HelpRequest::sum('people_count'),
            'recent' => HelpRequest::orderBy('created_at', 'desc')->limit(10)->get()
        ];

        // Users Analytics
        $usersData = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'citizens' => User::where('role', 'citizen')->count(),
            'relief_workers' => User::where('role', 'relief_worker')->count(),
            'active' => User::where('is_active', 1)->count(),
        ];

        // Assignments Analytics
        $assignmentsData = [
            'total' => Assignment::count(),
            'active' => Assignment::where('status', 'Assigned')->count(),
            'checked_in' => Assignment::where('status', 'Checked In')->count(),
            'checked_out' => Assignment::where('status', 'Checked Out')->count(),
        ];

        // Time-based analytics (last 7 days)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $last7Days[] = [
                'date' => Carbon::now()->subDays($i)->format('M d'),
                'requests' => HelpRequest::whereDate('created_at', $date)->count(),
                'assignments' => Assignment::whereDate('created_at', $date)->count(),
            ];
        }

        return [
            'alerts' => $alertsData,
            'shelters' => $sheltersData,
            'requests' => $requestsData,
            'users' => $usersData,
            'assignments' => $assignmentsData,
            'trends' => $last7Days,
        ];
    }

    /**
     * Export PDF Report
     */
    public function exportPDF()
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        $data = $this->getAnalyticsData();
        $data['generated_at'] = Carbon::now()->format('F d, Y h:i A');
        $data['generated_by'] = Auth::user()->name;

        // Generate PDF
        $pdf = Pdf::loadView('admin.reports.pdf-report', $data);
        
        // Download with timestamp
        $filename = 'Disaster_Alert_Report_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export TXT Report
     */
    public function exportTXT()
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        $data = $this->getAnalyticsData();
        
        // Build text content
        $content = "==============================================\n";
        $content .= "   DISASTER ALERT SYSTEM - ANALYTICS REPORT\n";
        $content .= "==============================================\n\n";
        
        $content .= "Generated: " . Carbon::now()->format('F d, Y h:i A') . "\n";
        $content .= "Generated By: " . Auth::user()->name . "\n\n";
        
        $content .= "----------------------------------------------\n";
        $content .= " ALERTS SUMMARY\n";
        $content .= "----------------------------------------------\n";
        $content .= "Total Alerts: {$data['alerts']['total']}\n";
        $content .= "Active Alerts: {$data['alerts']['active']}\n";
        $content .= "Resolved Alerts: {$data['alerts']['resolved']}\n\n";
        
        $content .= "By Severity:\n";
        foreach ($data['alerts']['by_severity'] as $severity => $count) {
            $content .= "  - {$severity}: {$count}\n";
        }
        $content .= "\n";
        
        $content .= "By Type:\n";
        foreach ($data['alerts']['by_type'] as $type => $count) {
            $content .= "  - {$type}: {$count}\n";
        }
        $content .= "\n";
        
        $content .= "----------------------------------------------\n";
        $content .= " SHELTERS SUMMARY\n";
        $content .= "----------------------------------------------\n";
        $content .= "Total Shelters: {$data['shelters']['total']}\n";
        $content .= "Active Shelters: {$data['shelters']['active']}\n";
        $content .= "Full Shelters: {$data['shelters']['full']}\n";
        $content .= "Inactive Shelters: {$data['shelters']['inactive']}\n\n";
        
        $content .= "Capacity Details:\n";
        $content .= "  - Total Capacity: {$data['shelters']['total_capacity']} people\n";
        $content .= "  - Currently Occupied: {$data['shelters']['total_occupied']} people\n";
        $content .= "  - Available Space: {$data['shelters']['available_space']} people\n";
        $content .= "  - Occupancy Rate: {$data['shelters']['occupancy_rate']}%\n\n";
        
        $content .= "Shelter List:\n";
        foreach ($data['shelters']['list'] as $shelter) {
            $content .= "  - {$shelter->name}\n";
            $content .= "    Location: {$shelter->address}, {$shelter->city}\n";
            $content .= "    Capacity: {$shelter->current_occupancy}/{$shelter->capacity}\n";
            $content .= "    Status: {$shelter->status}\n\n";
        }
        
        $content .= "----------------------------------------------\n";
        $content .= " EMERGENCY REQUESTS SUMMARY\n";
        $content .= "----------------------------------------------\n";
        $content .= "Total Requests: {$data['requests']['total']}\n";
        $content .= "Pending: {$data['requests']['pending']}\n";
        $content .= "Assigned: {$data['requests']['assigned']}\n";
        $content .= "In Progress: {$data['requests']['in_progress']}\n";
        $content .= "Completed: {$data['requests']['completed']}\n";
        $content .= "Cancelled: {$data['requests']['cancelled']}\n\n";
        
        $content .= "Total People Affected: {$data['requests']['total_people']}\n\n";
        
        $content .= "By Request Type:\n";
        foreach ($data['requests']['by_type'] as $type => $count) {
            $content .= "  - {$type}: {$count}\n";
        }
        $content .= "\n";
        
        $content .= "By Urgency:\n";
        foreach ($data['requests']['by_urgency'] as $urgency => $count) {
            $content .= "  - {$urgency}: {$count}\n";
        }
        $content .= "\n";
        
        $content .= "----------------------------------------------\n";
        $content .= " USERS SUMMARY\n";
        $content .= "----------------------------------------------\n";
        $content .= "Total Users: {$data['users']['total']}\n";
        $content .= "Admins: {$data['users']['admins']}\n";
        $content .= "Citizens: {$data['users']['citizens']}\n";
        $content .= "Relief Workers: {$data['users']['relief_workers']}\n";
        $content .= "Active Users: {$data['users']['active']}\n\n";
        
        $content .= "----------------------------------------------\n";
        $content .= " ASSIGNMENTS SUMMARY\n";
        $content .= "----------------------------------------------\n";
        $content .= "Total Assignments: {$data['assignments']['total']}\n";
        $content .= "Active: {$data['assignments']['active']}\n";
        $content .= "Checked In: {$data['assignments']['checked_in']}\n";
        $content .= "Checked Out: {$data['assignments']['checked_out']}\n\n";
        
        $content .= "==============================================\n";
        $content .= "           END OF REPORT\n";
        $content .= "==============================================\n";
        
        // Create response
        $filename = 'Disaster_Alert_Report_' . Carbon::now()->format('Y-m-d_His') . '.txt';
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
