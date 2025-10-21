<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LanguageController;

// Public Dashboard route
Route::get('/', [AlertController::class, 'dashboard'])->name('dashboard')->middleware('nocache');

// Alert routes (public access)
Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index')->middleware('nocache');
Route::get('/alerts/{id}', [AlertController::class, 'show'])->name('alerts.show');

// Shelter routes (public access)
Route::get('/shelters', [ShelterController::class, 'index'])->name('shelters.index');
Route::get('/shelters/{id}', [ShelterController::class, 'show'])->name('shelters.show');

// Request routes (public access for emergency)
Route::get('/request-help', [RequestController::class, 'create'])->name('requests.create');
Route::post('/request-help', [RequestController::class, 'store'])->name('requests.store');
Route::get('/request/{id}', [RequestController::class, 'show'])->name('requests.show');
Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Language switcher route
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Role-based dashboard routes
Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/citizen/dashboard', [AuthController::class, 'citizenDashboard'])->name('citizen.dashboard');
Route::get('/relief/dashboard', [AuthController::class, 'reliefDashboard'])->name('relief.dashboard');

// Admin routes (protected) - Apply nocache to prevent stale header/nav rendering
Route::prefix('admin')->middleware('nocache')->group(function () {
    // Admin Dashboard
    Route::get('/alerts', [AlertController::class, 'adminIndex'])->name('admin.alerts');
    Route::get('/shelters', [ShelterController::class, 'adminIndex'])->name('admin.shelters');
    Route::get('/requests', [RequestController::class, 'adminIndex'])->name('admin.requests');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/analytics/export/pdf', [AnalyticsController::class, 'exportPDF'])->name('analytics.export.pdf');
    Route::get('/analytics/export/txt', [AnalyticsController::class, 'exportTXT'])->name('analytics.export.txt');
    Route::get('/notifications', function () {
        return view('admin.notifications');
    })->name('admin.notifications');
    
    // Alert CRUD Operations
    Route::get('/alerts/create', [AlertController::class, 'create'])->name('admin.alerts.create');
    Route::post('/alerts', [AlertController::class, 'store'])->name('admin.alerts.store');
    Route::get('/alerts/{id}/edit', [AlertController::class, 'edit'])->name('admin.alerts.edit');
    Route::put('/alerts/{id}', [AlertController::class, 'update'])->name('admin.alerts.update');
    Route::delete('/alerts/{id}', [AlertController::class, 'destroy'])->name('admin.alerts.destroy');
    
    // Shelter CRUD Operations
    Route::get('/shelters/create', [ShelterController::class, 'create'])->name('admin.shelters.create');
    Route::post('/shelters', [ShelterController::class, 'store'])->name('admin.shelters.store');
    Route::get('/shelters/{id}/edit', [ShelterController::class, 'edit'])->name('admin.shelters.edit');
    Route::put('/shelters/{id}', [ShelterController::class, 'update'])->name('admin.shelters.update');
    Route::delete('/shelters/{id}', [ShelterController::class, 'destroy'])->name('admin.shelters.destroy');
    
    // Request Management Operations
    Route::get('/requests/{id}/assign', [RequestController::class, 'showAssign'])->name('admin.requests.assign');
    Route::post('/requests/{id}/assign', [RequestController::class, 'assign'])->name('admin.requests.assign.store');
    Route::post('/requests/bulk-assign', [RequestController::class, 'bulkAssign'])->name('admin.requests.bulk-assign');
    Route::put('/requests/{id}/status', [RequestController::class, 'updateStatus'])->name('admin.requests.update-status');
});

// Citizen routes (protected)
Route::prefix('citizen')->middleware('nocache')->group(function () {
    Route::get('/my-requests', [RequestController::class, 'citizenDashboard'])->name('citizen.requests');
});

// Alternative route name for backward compatibility
Route::get('/citizen/my-requests', [RequestController::class, 'citizenDashboard'])->name('requests.citizen-dashboard');

// Inbox Notification Routes - Apply nocache for fresh notification counts
Route::middleware('nocache')->group(function () {
    Route::get('/admin/inbox', [\App\Http\Controllers\NotificationController::class, 'adminInbox'])->name('admin.inbox');
    Route::get('/citizen/inbox', [\App\Http\Controllers\NotificationController::class, 'citizenInbox'])->name('citizen.inbox');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/admin/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAdminAsRead'])->name('notifications.admin.read-all');
    Route::post('/notifications/citizen/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllCitizenAsRead'])->name('notifications.citizen.read-all');
    Route::get('/api/notifications/unseen-count', [\App\Http\Controllers\NotificationController::class, 'getUnseenCount'])->name('notifications.unseen-count');
});

// Push Notification API Routes - No nocache for API endpoints (they need normal caching)
Route::prefix('api/notifications')->group(function () {
    Route::post('/subscribe', [\App\Http\Controllers\NotificationController::class, 'subscribe'])->name('notifications.subscribe');
    Route::post('/unsubscribe', [\App\Http\Controllers\NotificationController::class, 'unsubscribe'])->name('notifications.unsubscribe');
    Route::post('/preferences', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::get('/preferences', [\App\Http\Controllers\NotificationController::class, 'getPreferences'])->name('notifications.preferences.get');
    Route::post('/test', [\App\Http\Controllers\NotificationController::class, 'sendTest'])->name('notifications.test');
});

// Default welcome route (for reference)
Route::get('/welcome', function () {
    return view('welcome');
});

// API endpoint for dashboard statistics
Route::get('/api/dashboard-stats', function () {
    $stats = [
        'total_alerts' => \App\Models\Alert::count(),
        'pending' => \App\Models\HelpRequest::where('status', 'Pending')->count(),
        'in_progress' => \App\Models\HelpRequest::where('status', 'In Progress')->count(),
        'completed' => \App\Models\HelpRequest::where('status', 'Completed')->count(),
    ];
    
    return response()->json([
        'stats' => $stats,
        'new_requests' => [], // For now, empty
        'timestamp' => now()->toDateTimeString()
    ]);
});

// API endpoint for push notification subscriptions
Route::post('/api/push-subscription', function (\Illuminate\Http\Request $request) {
    // In a real app, save to database
    // For now, just return success
    \Illuminate\Support\Facades\Log::info('Push subscription received:', $request->all());
    
    return response()->json([
        'success' => true,
        'message' => 'Subscription saved successfully'
    ]);
});

Route::delete('/api/push-subscription', function () {
    // In a real app, remove from database
    \Illuminate\Support\Facades\Log::info('Push subscription removed');
    
    return response()->json([
        'success' => true,
        'message' => 'Subscription removed successfully'
    ]);
});

// Simple test page with buttons
Route::get('/test-dashboard', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Dashboard Test Page</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .test-button { 
                display: block; 
                margin: 10px 0; 
                padding: 15px 20px; 
                background: #3498db; 
                color: white; 
                text-decoration: none; 
                border-radius: 5px; 
                width: 300px; 
                text-align: center;
            }
            .test-button:hover { background: #2980b9; }
            .result { 
                margin: 10px 0; 
                padding: 10px; 
                border: 1px solid #ddd; 
                border-radius: 5px; 
                background: #f9f9f9; 
            }
        </style>
    </head>
    <body>
        <h1>🧪 Dashboard Testing Page</h1>
        <p>Use these buttons to test the real-time dashboard features:</p>
        
        <a href="/test-pusher" target="_blank" class="test-button">
            🚨 Test New Request Notification
        </a>
        
        <a href="/test-status-update" target="_blank" class="test-button">
            🔄 Test Status Update
        </a>
        
        <a href="/admin/dashboard" target="_blank" class="test-button">
            📊 Open Admin Dashboard
        </a>
        
        <button onclick="testMultipleUpdates()" class="test-button" style="border: none; cursor: pointer;">
            🔄 Test Multiple Status Updates
        </button>
        
        <div id="results"></div>
        
        <script>
            async function testMultipleUpdates() {
                const results = document.getElementById("results");
                results.innerHTML = "<h3>Testing Multiple Updates...</h3>";
                
                for(let i = 1; i <= 3; i++) {
                    try {
                        const response = await fetch("/test-status-update");
                        const data = await response.json();
                        
                        results.innerHTML += `
                            <div class="result">
                                <strong>Test ${i}:</strong> ${data.message}<br>
                                Request ID: ${data.data.request_id}<br>
                                Status Change: ${data.data.old_status} → ${data.data.new_status}
                            </div>
                        `;
                        
                        // Wait 2 seconds between tests
                        await new Promise(resolve => setTimeout(resolve, 2000));
                    } catch (error) {
                        results.innerHTML += `<div class="result">❌ Test ${i} failed: ${error.message}</div>`;
                    }
                }
                
                results.innerHTML += "<div class=\"result\">✅ All tests completed! Check the admin dashboard for updates.</div>";
            }
        </script>
    </body>
    </html>
    ';
});

// Test route for Pusher real-time functionality
Route::get('/test-pusher', function () {
    // Create a test emergency request in the database
    $testRequest = \App\Models\HelpRequest::create([
        'name' => 'Test Emergency User',
        'phone' => '+8801234567890',
        'location' => 'Test Location, Dhaka',
        'request_type' => 'Shelter',
        'urgency' => 'Critical',
        'people_count' => 3,
        'description' => 'This is a test emergency request for real-time notifications',
        'latitude' => 23.8103,
        'longitude' => 90.4125,
        'status' => 'Pending',
        'user_id' => 1
    ]);
    
    return response()->json([
        'message' => 'Test emergency request created in database!',
        'instruction' => 'Refresh the admin dashboard to see the new request',
        'data' => [
            'id' => $testRequest->id,
            'name' => $testRequest->name,
            'type' => $testRequest->request_type,
            'urgency' => $testRequest->urgency,
            'location' => $testRequest->location,
            'status' => $testRequest->status
        ]
    ]);
});

// Test route for status update functionality
Route::get('/test-status-update', function () {
    // Get the most recent request or create one
    $request = \App\Models\HelpRequest::orderBy('id', 'desc')->first();
    
    if (!$request) {
        $request = \App\Models\HelpRequest::create([
            'name' => 'Test Status User',
            'phone' => '+8801234567890',
            'location' => 'Test Location, Dhaka',
            'request_type' => 'Shelter',
            'urgency' => 'Medium',
            'people_count' => 2,
            'description' => 'Test request for status updates',
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'status' => 'Pending',
            'user_id' => 1
        ]);
    }
    
    $oldStatus = $request->status;
    $statuses = ['Pending', 'Assigned', 'In Progress', 'Completed'];
    
    // Cycle to next status
    $currentIndex = array_search($oldStatus, $statuses);
    $nextIndex = ($currentIndex + 1) % count($statuses);
    $newStatus = $statuses[$nextIndex];
    
    // Update status in database
    $request->update(['status' => $newStatus]);
    
    return response()->json([
        'message' => 'Status updated in database!',
        'instruction' => 'Refresh the admin dashboard to see the status change',
        'data' => [
            'request_id' => $request->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'name' => $request->name,
            'type' => $request->request_type
        ]
    ]);
});

// Test route for status update real-time functionality
Route::get('/test-status-update', function () {
    // Get the first available request
    $request = \App\Models\HelpRequest::first();
    
    if (!$request) {
        return response()->json([
            'error' => 'No requests found in database. Create one first.'
        ], 404);
    }
    
    $oldStatus = $request->status;
    
    // Cycle through statuses
    $statusCycle = ['Pending', 'Assigned', 'In Progress', 'Completed'];
    $currentIndex = array_search($oldStatus, $statusCycle);
    $newStatus = $statusCycle[($currentIndex + 1) % count($statusCycle)];
    
    $request->status = $newStatus;
    $request->save();
    
    // Broadcast test status update event
    event(new \App\Events\RequestStatusUpdated($request, $oldStatus, $newStatus));
    
    return response()->json([
        'message' => 'Test status update sent!',
        'instruction' => 'Check the admin dashboard for the live status update',
        'request_id' => $request->id,
        'old_status' => $oldStatus,
        'new_status' => $newStatus,
        'data' => $request->toArray()
    ]);
});

// Test route for push notifications
Route::get('/test-push-notification', function () {
    // Send browser push notification to all active subscriptions
    $subscriptions = \App\Models\PushSubscription::where('is_active', true)->get();
    
    $count = 0;
    foreach ($subscriptions as $subscription) {
        // Check if should receive notification
        if ($subscription->shouldReceiveNotification('Shelter', 'Critical')) {
            // In a real implementation, send actual push notification here
            $count++;
        }
    }
    
    return response()->json([
        'message' => 'Push notification test completed!',
        'instruction' => 'Check your browser for push notifications',
        'subscriptions_found' => $subscriptions->count(),
        'notifications_sent' => $count,
        'note' => 'Actual push sending requires Web Push library setup'
    ]);
});
