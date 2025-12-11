<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Welcome page (public)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Logout route (authenticated only)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Staff routes (Staff role only)
Route::middleware(['auth', 'role.staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
});

// Client routes (Client role only)
Route::middleware(['auth', 'role.client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
});

// Lead routes (Lead role only)
Route::middleware(['auth', 'role.lead'])->prefix('lead')->name('lead.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Lead\DashboardController::class, 'index'])->name('dashboard');
});

// Admin routes (Admin role only)
Route::middleware(['auth', 'role.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'create', 'show']);
    Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class);
    Route::post('leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convertToClient'])->name('leads.convert');
    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);
    Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
});
