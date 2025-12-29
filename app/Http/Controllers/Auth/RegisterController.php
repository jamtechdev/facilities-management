<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureAccess;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (auth()->check()) {
            return EnsureAccess::redirectToDashboard();
        }
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => ['required', 'in:client,staff'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'company_name' => ['required_if:role,client', 'nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store plain password before hashing for email
        $plainPassword = $request->password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role based on selection
        if ($request->role === 'client') {
            $user->assignRole('Client');

            // Create client profile
            \App\Models\Client::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name ?? $request->name,
                'contact_person' => $request->name,
                'email' => $request->email,
                'is_active' => true,
            ]);
        } elseif ($request->role === 'staff') {
            $user->assignRole('Staff');

            // Create staff profile
            \App\Models\Staff::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => true,
            ]);
        }

        // Send notifications (with error handling - don't fail registration if notifications fail)
        try {
            $notificationService = app(NotificationService::class);

            // Send registration email to user with credentials
            $notificationService->sendUserRegistrationEmail($user, $plainPassword, $request->role);

            // Notify admins about new user registration
            $notificationService->notifyAdminsNewUser($user, $request->role);
        } catch (\Exception $e) {
            // Log error but don't fail registration
            \Illuminate\Support\Facades\Log::error('Notification sending failed during registration', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        auth()->login($user);

        return EnsureAccess::redirectToDashboard();
    }
}
