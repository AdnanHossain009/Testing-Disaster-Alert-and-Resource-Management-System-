<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlertController extends Controller
{

    // displaying a listing of alerts demo not real

    public function index()
    {
        // sample alert data for demonstration


        $alerts = [
            [
                'id' => 1,
                'title' => 'Flood Warning - Dhaka',
                'description' => 'Heavy rainfall expected. Citizens advised to stay indoors.',
                'severity' => 'High',
                'location' => 'Dhaka, Bangladesh',
                'created_at' => '2025-09-10 10:00:00'
            ],

            [
                'id' => 2,
                'title' => 'Earthquake Alert - Chittagong',
                'description' => 'Minor earthquake detected. No immediate danger.',
                'severity' => 'Medium',
                'location' => 'Chittagong, Bangladesh',
                'created_at' => '2025-09-10 08:30:00'
            ],

            [
                'id' => 3,
                'title' => 'Cyclone Watch - Cox\'s Bazar',
                'description' => 'Cyclone approaching coastal areas. Evacuation recommended.',
                'severity' => 'High',
                'location' => 'Cox\'s Bazar, Bangladesh',
                'created_at' => '2025-09-10 06:15:00'
            ]
        ];

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
}
