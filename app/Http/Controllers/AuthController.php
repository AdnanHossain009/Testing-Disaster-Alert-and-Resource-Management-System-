<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // redirect based on role
            switch ($user->role) {
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

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    
    // handle registration
     
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,citizen,relief_worker',
            'address' => 'nullable|string'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'address' => $validated['address'] ?? null
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }


     // handle logout
     
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('dashboard')->with('message', 'Successfully logged out');
    }

    
     // admin Dashboard
     
    public function adminDashboard()
    {
        // check if user is admin using Laravel Auth
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // admin dashboard data with real Eloquent queries
        $stats = [
            'total_alerts' => \App\Models\Alert::count(),
            'active_alerts' => \App\Models\Alert::active()->count(),
            'total_shelters' => \App\Models\Shelter::count(),
            'available_shelters' => \App\Models\Shelter::where('status', 'Active')
                ->whereRaw('capacity > current_occupancy')->count(),
            'pending_requests' => \App\Models\Request::where('status', 'Pending')->count(),
            'assigned_requests' => \App\Models\Request::where('status', 'Assigned')->count(),
            'total_requests' => \App\Models\Request::count()
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
        // check if user is citizen using Laravel Auth
        if (!Auth::check() || Auth::user()->role !== 'citizen') {
            return redirect()->route('login');
        }

        // citizen dashboard data with real queries
        $activeAlerts = \App\Models\Alert::active()->recent()->limit(5)->get()->map(function($alert) {
            return [
                'id' => $alert->id,
                'title' => $alert->title,
                'severity' => $alert->severity,
                'issued' => $alert->created_at->diffForHumans()
            ];
        });

        $myRequests = \App\Models\Request::with('assignment.shelter')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($req) {
                return [
                    'id' => $req->id,
                    'emergency_type' => $req->request_type,
                    'status' => $req->status,
                    'shelter' => $req->assignment ? $req->assignment->shelter->name : 'Not assigned',
                    'submitted' => $req->created_at->diffForHumans()
                ];
            });

        $nearestShelters = \App\Models\Shelter::where('status', 'Active')
            ->whereRaw('capacity > current_occupancy')
            ->limit(5)
            ->get()
            ->map(function($shelter) {
                $available = $shelter->capacity - $shelter->current_occupancy;
                return [
                    'name' => $shelter->name,
                    'distance' => '2.3 km', // Would calculate real distance in production
                    'availability' => $available > 10 ? 'Available' : 'Nearly Full',
                    'capacity' => $available . '/' . $shelter->capacity
                ];
            });

        return view('citizen.dashboard', compact('activeAlerts', 'myRequests', 'nearestShelters'));
    }

    
    //  relief Worker Dashboard
    
    public function reliefDashboard()
    {
        // check if user is relief worker using Laravel Auth
        if (!Auth::check() || Auth::user()->role !== 'relief_worker') {
            return redirect()->route('login');
        }

        // relief worker dashboard data with real queries
        $assignedShelters = \App\Models\Shelter::where('status', 'Active')
            ->limit(5)
            ->get()
            ->map(function($shelter) {
                return [
                    'name' => $shelter->name,
                    'current_occupancy' => $shelter->current_occupancy . '/' . $shelter->capacity,
                    'status' => $shelter->status,
                    'location' => $shelter->city . ', ' . $shelter->state
                ];
            });

        $taskList = [
            'Conduct headcount at assigned shelters',
            'Distribute food supplies to new arrivals',
            'Update shelter capacity status',
            'Coordinate with medical team'
        ];

        return view('relief.dashboard', compact('assignedShelters', 'taskList'));
    }

    /**
     * Admin Analytics Page
     */
    public function adminAnalytics()
    {
        // check if user is admin using Laravel Auth
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Analytics data
        $analytics = [
            'alerts' => [
                'total' => \App\Models\Alert::count(),
                'by_severity' => [
                    'Critical' => \App\Models\Alert::where('severity', 'Critical')->count(),
                    'High' => \App\Models\Alert::where('severity', 'High')->count(),
                    'Medium' => \App\Models\Alert::where('severity', 'Medium')->count(),
                    'Low' => \App\Models\Alert::where('severity', 'Low')->count(),
                ],
                'by_type' => [
                    'Flood' => \App\Models\Alert::where('type', 'Flood')->count(),
                    'Earthquake' => \App\Models\Alert::where('type', 'Earthquake')->count(),
                    'Cyclone' => \App\Models\Alert::where('type', 'Cyclone')->count(),
                ]
            ],
            'requests' => [
                'total' => \App\Models\Request::count(),
                'by_status' => [
                    'Pending' => \App\Models\Request::where('status', 'Pending')->count(),
                    'Assigned' => \App\Models\Request::where('status', 'Assigned')->count(),
                    'Completed' => \App\Models\Request::where('status', 'Completed')->count(),
                ],
                'by_type' => [
                    'Shelter' => \App\Models\Request::where('request_type', 'Shelter')->count(),
                    'Medical' => \App\Models\Request::where('request_type', 'Medical')->count(),
                    'Food' => \App\Models\Request::where('request_type', 'Food')->count(),
                    'Rescue' => \App\Models\Request::where('request_type', 'Rescue')->count(),
                ]
            ],
            'shelters' => [
                'total' => \App\Models\Shelter::count(),
                'active' => \App\Models\Shelter::where('status', 'Active')->count(),
                'capacity_utilization' => \App\Models\Shelter::selectRaw('SUM(current_occupancy) as occupied, SUM(capacity) as total')->first(),
            ]
        ];

        return view('admin.analytics', compact('analytics'));
    }
}
