<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            return $this->redirectToDashboard();
        }
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign Lead role by default for new registrations
        $user->assignRole('Lead');

        auth()->login($user);

        return redirect()->route('lead.dashboard');
    }

    /**
     * Redirect user to their appropriate dashboard.
     */
    protected function redirectToDashboard()
    {
        $user = auth()->user();

        // Redirect based on dashboard permissions (priority order)
        if ($user->can('view admin dashboard')) {
            if ($user->can('view roles')) {
                return redirect()->route('superadmin.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }

        if ($user->can('view staff dashboard')) {
            return redirect()->route('staff.dashboard');
        }

        if ($user->can('view client dashboard')) {
            return redirect()->route('client.dashboard');
        }

        if ($user->can('view lead dashboard')) {
            return redirect()->route('lead.dashboard');
        }

        return redirect()->route('welcome');
    }
}
