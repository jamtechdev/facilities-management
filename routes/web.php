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
        $user = auth()->user();
        // Redirect authenticated users to their dashboard based on permissions
        if ($user->can('view admin dashboard')) {
            if ($user->can('view roles')) {
                return redirect()->route('superadmin.dashboard');
            }
            return redirect()->route('admin.dashboard');
        } elseif ($user->can('view staff dashboard')) {
            return redirect()->route('staff.dashboard');
        } elseif ($user->can('view client dashboard')) {
            return redirect()->route('client.dashboard');
        } elseif ($user->can('view lead dashboard')) {
            return redirect()->route('lead.dashboard');
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

// Staff routes - Permission-based access
Route::middleware(['auth', 'permission:view staff dashboard'])->prefix('staff')->name('staff.')->group(function () {
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

// Client routes - Permission-based access
Route::middleware(['auth', 'permission:view client dashboard'])->prefix('client')->name('client.')->group(function () {
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

// Lead routes - Permission-based access
Route::middleware(['auth', 'permission:view lead dashboard'])->prefix('lead')->name('lead.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Lead\DashboardController::class, 'index'])->name('dashboard');
});

// SuperAdmin routes - Permission-based access (SuperAdmin role only)
Route::middleware(['auth', 'role.superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/documents', [\App\Http\Controllers\Admin\ProfileController::class, 'uploadDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteDocument'])->name('profile.documents.destroy');
    Route::get('/profile/documents/{document}/download', [\App\Http\Controllers\Admin\ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
    
    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/notifications', [\App\Http\Controllers\Admin\SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::post('/settings/messages', [\App\Http\Controllers\Admin\SettingsController::class, 'updateMessages'])->name('settings.messages.update');
    Route::post('/settings/general', [\App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('settings.general.update');
    Route::post('/settings/project', [\App\Http\Controllers\Admin\SettingsController::class, 'updateProject'])->name('settings.project.update');

    // Business routes - Permission based
    // Leads routes
    Route::middleware('permission:view leads')->group(function () {
        Route::get('leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
        Route::get('leads/create', [\App\Http\Controllers\Admin\LeadController::class, 'create'])->name('leads.create');
        Route::post('leads', [\App\Http\Controllers\Admin\LeadController::class, 'store'])->name('leads.store');
        Route::post('leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convertToClient'])->name('leads.convert');
    });
    Route::middleware('permission:view lead details')->group(function () {
        Route::get('leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('leads.show');
    });
    Route::middleware('permission:edit leads')->group(function () {
        Route::get('leads/{lead}/edit', [\App\Http\Controllers\Admin\LeadController::class, 'edit'])->name('leads.edit');
        Route::put('leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'update'])->name('leads.update');
    });
    Route::middleware('permission:delete leads')->group(function () {
        Route::delete('leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('leads.destroy');
    });

    // Clients routes
    Route::middleware('permission:view clients')->group(function () {
        Route::get('clients', [\App\Http\Controllers\Admin\ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/create', [\App\Http\Controllers\Admin\ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [\App\Http\Controllers\Admin\ClientController::class, 'store'])->name('clients.store');
    });
    Route::middleware('permission:view client details')->group(function () {
        Route::get('clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'show'])->name('clients.show');
    });
    Route::middleware('permission:edit clients')->group(function () {
        Route::get('clients/{client}/edit', [\App\Http\Controllers\Admin\ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'update'])->name('clients.update');
    });
    Route::middleware('permission:delete clients')->group(function () {
        Route::delete('clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Staff routes
    Route::middleware('permission:view staff')->group(function () {
        Route::get('staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/create', [\App\Http\Controllers\Admin\StaffController::class, 'create'])->name('staff.create');
        Route::post('staff', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('staff.store');
    });
    Route::middleware('permission:view staff details')->group(function () {
        Route::get('staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'show'])->name('staff.show');
    });
    Route::middleware('permission:edit staff')->group(function () {
        Route::get('staff/{staff}/edit', [\App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('staff.edit');
        Route::put('staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->name('staff.update');
    });
    Route::middleware('permission:delete staff')->group(function () {
        Route::delete('staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // Financial routes - Invoices
    Route::middleware('permission:view invoices')->group(function () {
        Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/create', [\App\Http\Controllers\Admin\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'downloadPdf'])->name('invoices.download');
    });
    Route::middleware('permission:view invoice details')->group(function () {
        Route::get('invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    });
    Route::middleware('permission:edit invoices')->group(function () {
        Route::get('invoices/{invoice}/edit', [\App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('invoices.update');
        Route::put('/invoices/{invoice}/status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    });
    Route::middleware('permission:delete invoices')->group(function () {
        Route::delete('invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });

    Route::middleware('permission:view payouts')->group(function () {
        Route::get('/payouts', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts/calculate', [\App\Http\Controllers\Admin\PayoutController::class, 'calculate'])->name('payouts.calculate');
        Route::get('/payouts/download', [\App\Http\Controllers\Admin\PayoutController::class, 'downloadPdf'])->name('payouts.download');
    });

    // Operations
    Route::middleware('permission:view inventory')->group(function () {
        Route::resource('inventory', \App\Http\Controllers\Admin\InventoryController::class);
        Route::post('/inventory/{inventory}/assign', [\App\Http\Controllers\Admin\InventoryController::class, 'assign'])->name('inventory.assign');
    });

    // System routes - Users
    Route::middleware('permission:view users')->group(function () {
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:view user details')->group(function () {
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('permission:edit users')->group(function () {
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:delete users')->group(function () {
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('permission:view roles')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles/permissions/update', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermission'])->name('roles.permissions.update');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
        
        // Permission management routes (SuperAdmin only)
        Route::post('/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // Communications
    Route::post('/communications', [\App\Http\Controllers\Admin\CommunicationController::class, 'store'])->name('communications.store');
    Route::put('/communications/{communication}', [\App\Http\Controllers\Admin\CommunicationController::class, 'update'])->name('communications.update');
    Route::delete('/communications/{communication}', [\App\Http\Controllers\Admin\CommunicationController::class, 'destroy'])->name('communications.destroy');

    // Documents
    Route::post('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');

    // Job Photos
    Route::post('/job-photos/{jobPhoto}/approve', [\App\Http\Controllers\Admin\JobPhotoController::class, 'approve'])->name('job-photos.approve');
    Route::post('/job-photos/{jobPhoto}/reject', [\App\Http\Controllers\Admin\JobPhotoController::class, 'reject'])->name('job-photos.reject');
    Route::get('/job-photos/{jobPhoto}/download', [\App\Http\Controllers\Admin\JobPhotoController::class, 'download'])->name('job-photos.download');
});

// Admin routes - Permission-based access (Admin role only)
Route::middleware(['auth', 'permission:view admin dashboard'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/documents', [\App\Http\Controllers\Admin\ProfileController::class, 'uploadDocument'])->name('profile.documents.store');
    Route::delete('/profile/documents/{document}', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteDocument'])->name('profile.documents.destroy');
    Route::get('/profile/documents/{document}/download', [\App\Http\Controllers\Admin\ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
    
    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/notifications', [\App\Http\Controllers\Admin\SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::post('/settings/messages', [\App\Http\Controllers\Admin\SettingsController::class, 'updateMessages'])->name('settings.messages.update');
    Route::post('/settings/general', [\App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('settings.general.update');
    Route::post('/settings/project', [\App\Http\Controllers\Admin\SettingsController::class, 'updateProject'])->name('settings.project.update');

    // Business routes - Permission based
    // Leads routes
    Route::middleware('permission:view leads')->group(function () {
        Route::get('leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
        Route::get('leads/create', [\App\Http\Controllers\Admin\LeadController::class, 'create'])->name('leads.create');
        Route::post('leads', [\App\Http\Controllers\Admin\LeadController::class, 'store'])->name('leads.store');
        Route::post('leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convertToClient'])->name('leads.convert');
    });
    Route::middleware('permission:view lead details')->group(function () {
        Route::get('leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('leads.show');
    });
    Route::middleware('permission:edit leads')->group(function () {
        Route::get('leads/{lead}/edit', [\App\Http\Controllers\Admin\LeadController::class, 'edit'])->name('leads.edit');
        Route::put('leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'update'])->name('leads.update');
    });
    Route::middleware('permission:delete leads')->group(function () {
        Route::delete('leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('leads.destroy');
    });

    // Clients routes
    Route::middleware('permission:view clients')->group(function () {
        Route::get('clients', [\App\Http\Controllers\Admin\ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/create', [\App\Http\Controllers\Admin\ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [\App\Http\Controllers\Admin\ClientController::class, 'store'])->name('clients.store');
    });
    Route::middleware('permission:view client details')->group(function () {
        Route::get('clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'show'])->name('clients.show');
    });
    Route::middleware('permission:edit clients')->group(function () {
        Route::get('clients/{client}/edit', [\App\Http\Controllers\Admin\ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'update'])->name('clients.update');
    });
    Route::middleware('permission:delete clients')->group(function () {
        Route::delete('clients/{client}', [\App\Http\Controllers\Admin\ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Staff routes
    Route::middleware('permission:view staff')->group(function () {
        Route::get('staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/create', [\App\Http\Controllers\Admin\StaffController::class, 'create'])->name('staff.create');
        Route::post('staff', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('staff.store');
    });
    Route::middleware('permission:view staff details')->group(function () {
        Route::get('staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'show'])->name('staff.show');
    });
    Route::middleware('permission:edit staff')->group(function () {
        Route::get('staff/{staff}/edit', [\App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('staff.edit');
        Route::put('staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->name('staff.update');
    });
    Route::middleware('permission:delete staff')->group(function () {
        Route::delete('staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // Financial routes - Invoices
    Route::middleware('permission:view invoices')->group(function () {
        Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/create', [\App\Http\Controllers\Admin\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'downloadPdf'])->name('invoices.download');
    });
    Route::middleware('permission:view invoice details')->group(function () {
        Route::get('invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    });
    Route::middleware('permission:edit invoices')->group(function () {
        Route::get('invoices/{invoice}/edit', [\App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('invoices.update');
        Route::put('/invoices/{invoice}/status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    });
    Route::middleware('permission:delete invoices')->group(function () {
        Route::delete('invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });

    Route::middleware('permission:view payouts')->group(function () {
        Route::get('/payouts', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts/calculate', [\App\Http\Controllers\Admin\PayoutController::class, 'calculate'])->name('payouts.calculate');
        Route::get('/payouts/download', [\App\Http\Controllers\Admin\PayoutController::class, 'downloadPdf'])->name('payouts.download');
    });

    // Operations
    Route::middleware('permission:view inventory')->group(function () {
        Route::resource('inventory', \App\Http\Controllers\Admin\InventoryController::class);
        Route::post('/inventory/{inventory}/assign', [\App\Http\Controllers\Admin\InventoryController::class, 'assign'])->name('inventory.assign');
    });

    // System routes - Users
    Route::middleware('permission:view users')->group(function () {
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:view user details')->group(function () {
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('permission:edit users')->group(function () {
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:delete users')->group(function () {
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('permission:view roles')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles/permissions/update', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermission'])->name('roles.permissions.update');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
        
        // Permission management routes (SuperAdmin only)
        Route::post('/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // Communications
    Route::post('/communications', [\App\Http\Controllers\Admin\CommunicationController::class, 'store'])->name('communications.store');
    Route::put('/communications/{communication}', [\App\Http\Controllers\Admin\CommunicationController::class, 'update'])->name('communications.update');
    Route::delete('/communications/{communication}', [\App\Http\Controllers\Admin\CommunicationController::class, 'destroy'])->name('communications.destroy');

    // Documents
    Route::post('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');

    // Job Photos
    Route::post('/job-photos/{jobPhoto}/approve', [\App\Http\Controllers\Admin\JobPhotoController::class, 'approve'])->name('job-photos.approve');
    Route::post('/job-photos/{jobPhoto}/reject', [\App\Http\Controllers\Admin\JobPhotoController::class, 'reject'])->name('job-photos.reject');
    Route::get('/job-photos/{jobPhoto}/download', [\App\Http\Controllers\Admin\JobPhotoController::class, 'download'])->name('job-photos.download');
});
