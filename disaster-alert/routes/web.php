<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AuthController;

// Public Dashboard route
Route::get('/', [AlertController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard', [AlertController::class, 'dashboard'])->name('dashboard.main');

// Alert routes (public access)
Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
Route::get('/alerts/{id}', [AlertController::class, 'show'])->name('alerts.show');

// Shelter routes (public access)
Route::get('/shelters', [ShelterController::class, 'index'])->name('shelters.index');
Route::get('/shelters/{id}', [ShelterController::class, 'show'])->name('shelters.show');

// Request routes (public access for emergency)
Route::get('/request-help', [RequestController::class, 'create'])->name('requests.create');
Route::post('/request-help', [RequestController::class, 'store'])->name('requests.store');
Route::get('/request/{id}', [RequestController::class, 'show'])->name('requests.show');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Role-based dashboard routes
Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/citizen/dashboard', [AuthController::class, 'citizenDashboard'])->name('citizen.dashboard');
Route::get('/relief/dashboard', [AuthController::class, 'reliefDashboard'])->name('relief.dashboard');

// Admin routes (protected)
Route::prefix('admin')->middleware('web')->group(function () {
    Route::get('/alerts', [AlertController::class, 'adminIndex'])->name('admin.alerts');
    Route::get('/alerts/create', [AlertController::class, 'create'])->name('admin.alerts.create');
    Route::post('/alerts', [AlertController::class, 'store'])->name('admin.alerts.store');
    Route::get('/alerts/{id}/edit', [AlertController::class, 'edit'])->name('admin.alerts.edit');
    Route::put('/alerts/{id}', [AlertController::class, 'update'])->name('admin.alerts.update');
    Route::delete('/alerts/{id}', [AlertController::class, 'destroy'])->name('admin.alerts.destroy');
    
    Route::get('/shelters', [ShelterController::class, 'adminIndex'])->name('admin.shelters');
    Route::get('/shelters/create', [ShelterController::class, 'create'])->name('admin.shelters.create');
    Route::post('/shelters', [ShelterController::class, 'store'])->name('admin.shelters.store');
    Route::get('/shelters/{id}/edit', [ShelterController::class, 'edit'])->name('admin.shelters.edit');
    Route::put('/shelters/{id}', [ShelterController::class, 'update'])->name('admin.shelters.update');
    Route::delete('/shelters/{id}', [ShelterController::class, 'destroy'])->name('admin.shelters.destroy');
    
    Route::get('/requests', [RequestController::class, 'adminIndex'])->name('admin.requests');
    Route::get('/requests/{id}/assign', [RequestController::class, 'assign'])->name('admin.requests.assign');
    Route::post('/requests/{id}/assign', [RequestController::class, 'storeAssignment'])->name('admin.requests.store_assignment');
});

// Citizen routes (protected)
Route::prefix('citizen')->middleware('web')->group(function () {
    Route::get('/my-requests', [RequestController::class, 'citizenDashboard'])->name('citizen.requests');
});

// Default welcome route (for reference)
Route::get('/welcome', function () {
    return view('welcome');
});

// SMS WEBHOOK AND API ROUTES
Route::prefix('api/sms')->group(function () {
    Route::post('/webhook', [App\Http\Controllers\SMSController::class, 'webhook'])->name('sms.webhook');
});

// ADMIN SMS MANAGEMENT ROUTES
Route::prefix('admin/sms')->middleware('web')->group(function () {
    Route::post('/send-alert', [App\Http\Controllers\SMSController::class, 'sendEmergencyAlert'])->name('admin.sms.send_alert');
    Route::post('/shelter-assignment', [App\Http\Controllers\SMSController::class, 'sendShelterAssignment'])->name('admin.sms.shelter_assignment');
    Route::post('/status-update', [App\Http\Controllers\SMSController::class, 'sendStatusUpdate'])->name('admin.sms.status_update');
    Route::post('/send-manual', [App\Http\Controllers\SMSController::class, 'sendManualSMS'])->name('admin.sms.send_manual');
    Route::get('/test', [App\Http\Controllers\SMSController::class, 'testConnectivity'])->name('admin.sms.test');
    Route::get('/statistics', [App\Http\Controllers\SMSController::class, 'getStatistics'])->name('admin.sms.statistics');
});
