<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AuthController;

// Public Dashboard route
Route::get('/', [AlertController::class, 'dashboard'])->name('dashboard');

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
Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');

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
    Route::get('/shelters', [ShelterController::class, 'adminIndex'])->name('admin.shelters');
    Route::get('/requests', [RequestController::class, 'adminIndex'])->name('admin.requests');
    Route::get('/analytics', [AuthController::class, 'adminAnalytics'])->name('admin.analytics');
});

// Citizen routes (protected)
Route::prefix('citizen')->middleware('web')->group(function () {
    Route::get('/my-requests', [RequestController::class, 'citizenDashboard'])->name('citizen.requests');
});

// Default welcome route (for reference)
Route::get('/welcome', function () {
    return view('welcome');
});
