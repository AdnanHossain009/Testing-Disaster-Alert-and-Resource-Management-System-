<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    
     // show login form
     
    public function showLogin()
    {
        return view('auth.login');
    }

    
     // show registration form
     
    public function showRegister()
    {
        return view('auth.register');
    }

    
     // handle login attempt
     
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // sample user authentication (in real app, use Laravel Auth)

        $users = [
            'admin@disaster.gov.bd' => [
                'password' => 'admin123',
                'role' => 'admin',
                'name' => 'Admin User'
            ],

            'citizen@example.com' => [
                'password' => 'citizen123',
                'role' => 'citizen',
                'name' => 'John Citizen'
            ],

            'relief@disaster.gov.bd' => [
                'password' => 'relief123',
                'role' => 'relief_worker',
                'name' => 'Relief Worker'
            ]
        ];

        if (isset($users[$email]) && $users[$email]['password'] === $password) {

            // simulating session storage

            session([
                'user' => [
                    'email' => $email,
                    'name' => $users[$email]['name'],
                    'role' => $users[$email]['role']
                ]
            ]);

            // redirect based on role

            switch ($users[$email]['role']) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'citizen':
                    return redirect()->route('citizen.dashboard');
                case 'relief_worker':
                    return redirect()->route('relief.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors(['error' => 'Invalid credentials']);
    }

    
    // handle registration
     
    public function register(Request $request)
    {

        // sample registration (in real app, save to database)\

        $userData = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'role' => $request->input('role', 'citizen'),
            'location' => $request->input('location')
        ];

        // simulate saving user

        session([
            'user' => $userData
        ]);

        return view('auth.register-success', compact('userData'));
    }


     // handle logout
     
    public function logout()
    {
        session()->forget('user');
        return redirect()->route('dashboard')->with('message', 'Successfully logged out');
    }

    
     // admin Dashboard
     
    public function adminDashboard()
    {
        // check if user is admin

        if (!session('user') || session('user')['role'] !== 'admin') {
            return redirect()->route('login');
        }

        // admin dashboard data
        
        $stats = [
            'total_alerts' => 3,
            'active_alerts' => 2,
            'total_shelters' => 4,
            'available_shelters' => 2,
            'pending_requests' => 1,
            'assigned_requests' => 2,
            'total_requests' => 4
        ];

        $recentActivity = [
            [
                'type' => 'request',
                'message' => 'New help request from John Rahman',
                'time' => '2 minutes ago',
                'priority' => 'high'
            ],

            [
                'type' => 'assignment',
                'message' => 'Auto-assigned Ahmed Hassan to Chittagong Sports Complex',
                'time' => '15 minutes ago',
                'priority' => 'medium'
            ],

            [
                'type' => 'alert',
                'message' => 'Flood alert issued for Dhaka region',
                'time' => '1 hour ago',
                'priority' => 'high'
            ]
        ];

        return view('admin.dashboard', compact('stats', 'recentActivity'));
    }

    
    // citizen Dashboard
     
    public function citizenDashboard()
    {
        // check if user is citizen

        if (!session('user') || session('user')['role'] !== 'citizen') {
            return redirect()->route('login');
        }

        // citizen dashboard data

        $activeAlerts = [
            [
                'id' => 1,
                'title' => 'Flood Warning - Dhaka',
                'severity' => 'High',
                'issued' => '2 hours ago'
            ],

            [
                'id' => 3,
                'title' => 'Cyclone Watch - Cox\'s Bazar',
                'severity' => 'High',
                'issued' => '6 hours ago'
            ]
        ];

        $myRequests = [
            [
                'id' => 1,
                'emergency_type' => 'Flood',
                'status' => 'Assigned',
                'shelter' => 'Dhaka Community Center',
                'submitted' => '1 hour ago'
            ]
        ];

        $nearestShelters = [
            [
                'name' => 'Dhaka Community Center',
                'distance' => '2.3 km',
                'availability' => 'Available',
                'capacity' => '155/200'
            ],

            [
                'name' => 'Gulshan Community Hall',
                'distance' => '3.7 km',
                'availability' => 'Nearly Full',
                'capacity' => '8/50'
            ]
        ];

        return view('citizen.dashboard', compact('activeAlerts', 'myRequests', 'nearestShelters'));
    }

    
    //  relief Worker Dashboard
    
    public function reliefDashboard()
    {
        // check if user is relief worker
        if (!session('user') || session('user')['role'] !== 'relief_worker') {
            return redirect()->route('login');
        }

        // relief worker dashboard data

        $assignedShelters = [
            [
                'name' => 'Dhaka Community Center',
                'current_occupancy' => '45/200',
                'status' => 'Active',
                'location' => 'Dhanmondi, Dhaka'
            ]
        ];

        $taskList = [
            'Conduct headcount at Dhaka Community Center',
            'Distribute food supplies to new arrivals',
            'Update shelter capacity status',
            'Coordinate with medical team'
        ];

        return view('relief.dashboard', compact('assignedShelters', 'taskList'));
    }
}
