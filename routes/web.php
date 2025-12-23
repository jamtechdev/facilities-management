<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController;

// Root route - redirect to login
Route::get('/', function () {
    if (auth()->check()) {
        // Redirect authenticated users to their dashboard based on role
        if (auth()->user()->hasRole('SuperAdmin')) {
            return redirect()->route('superadmin.dashboard');
        } elseif (auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('Staff')) {
            return redirect()->route('staff.dashboard');
        }
    }
    return redirect()->route('login');
})->name('welcome');

// Welcome page (public) - accessible via direct URL
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome.page');

// Public feedback form
Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'showForm'])->name('feedback.form');
Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');

// Public API for lead injection
Route::post('/api/leads', [\App\Http\Controllers\Api\LeadApiController::class, 'store'])->name('api.leads.store');

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
    Route::get('/profile', [\App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/documents', [\App\Http\Controllers\Staff\ProfileController::class, 'uploadDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [\App\Http\Controllers\Staff\ProfileController::class, 'deleteDocument'])->name('profile.documents.destroy');
    Route::get('/profile/documents/{document}/download', [\App\Http\Controllers\Staff\ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
    Route::get('/timesheet', [\App\Http\Controllers\Staff\TimesheetController::class, 'index'])->name('timesheet');
    Route::post('/timesheet/clock-in', [\App\Http\Controllers\Staff\TimesheetController::class, 'clockIn'])->name('timesheet.clock-in');
    Route::post('/timesheet/clock-out', [\App\Http\Controllers\Staff\TimesheetController::class, 'clockOut'])->name('timesheet.clock-out');
    Route::post('/job-photos', [\App\Http\Controllers\Staff\JobPhotoController::class, 'store'])->name('job-photos.store');
    Route::delete('/job-photos/{jobPhoto}', [\App\Http\Controllers\Staff\JobPhotoController::class, 'destroy'])->name('job-photos.destroy');
    Route::get('/activity', [\App\Http\Controllers\Staff\ActivityLogController::class, 'index'])->name('activity');
});

// Client routes (Client role only)
Route::middleware(['auth', 'role.client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/documents', [\App\Http\Controllers\Client\ProfileController::class, 'uploadDocument'])->name('profile.documents.store');
    Route::get('/profile/documents/{document}/download', [\App\Http\Controllers\Client\ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
    Route::delete('/profile/documents/{document}', [\App\Http\Controllers\Client\ProfileController::class, 'destroyDocument'])->name('profile.documents.destroy');
    Route::get('/services', [\App\Http\Controllers\Client\ServiceHistoryController::class, 'index'])->name('services');
    Route::get('/photos', [\App\Http\Controllers\Client\PhotoController::class, 'index'])->name('photos');
    Route::get('/feedback', [\App\Http\Controllers\Client\FeedbackController::class, 'index'])->name('feedback');
    Route::get('/documents', [\App\Http\Controllers\Client\DocumentController::class, 'documents'])->name('documents');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Client\DocumentController::class, 'download'])->name('documents.download');
    Route::get('/invoices', [\App\Http\Controllers\Client\InvoiceController::class, 'index'])->name('invoices');

});

// Lead routes (Lead role only)
Route::middleware(['auth', 'role.lead'])->prefix('lead')->name('lead.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Lead\DashboardController::class, 'index'])->name('dashboard');
});

// SuperAdmin routes (SuperAdmin role only)
Route::middleware(['auth', 'role.superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/documents', [\App\Http\Controllers\Admin\ProfileController::class, 'uploadDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteDocument'])->name('profile.documents.destroy');
    Route::get('/profile/documents/{document}/download', [\App\Http\Controllers\Admin\ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
});

// Admin routes (Admin and SuperAdmin roles)
Route::middleware(['auth', 'role.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/documents', [\App\Http\Controllers\Admin\ProfileController::class, 'uploadDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteDocument'])->name('profile.documents.destroy');
    Route::get('/profile/documents/{document}/download', [\App\Http\Controllers\Admin\ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class);
    Route::post('leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convertToClient'])->name('leads.convert');
    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);
    Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);

    // Communications
    Route::post('/communications', [\App\Http\Controllers\Admin\CommunicationController::class, 'store'])->name('communications.store');
    Route::put('/communications/{communication}', [\App\Http\Controllers\Admin\CommunicationController::class, 'update'])->name('communications.update');
    Route::delete('/communications/{communication}', [\App\Http\Controllers\Admin\CommunicationController::class, 'destroy'])->name('communications.destroy');

    // Documents
    Route::post('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');

    // Invoices
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
    Route::put('/invoices/{invoice}/status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'downloadPdf'])->name('invoices.download');

    // Job Photos
    Route::post('/job-photos/{jobPhoto}/approve', [\App\Http\Controllers\Admin\JobPhotoController::class, 'approve'])->name('job-photos.approve');
    Route::post('/job-photos/{jobPhoto}/reject', [\App\Http\Controllers\Admin\JobPhotoController::class, 'reject'])->name('job-photos.reject');
    Route::get('/job-photos/{jobPhoto}/download', [\App\Http\Controllers\Admin\JobPhotoController::class, 'download'])->name('job-photos.download');

    // Payouts
    Route::get('/payouts', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/calculate', [\App\Http\Controllers\Admin\PayoutController::class, 'calculate'])->name('payouts.calculate');
    Route::get('/payouts/download', [\App\Http\Controllers\Admin\PayoutController::class, 'downloadPdf'])->name('payouts.download');

    // Inventory
    Route::resource('inventory', \App\Http\Controllers\Admin\InventoryController::class);
    Route::post('/inventory/{inventory}/assign', [\App\Http\Controllers\Admin\InventoryController::class, 'assign'])->name('inventory.assign');

    // Roles & Permissions
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
});
