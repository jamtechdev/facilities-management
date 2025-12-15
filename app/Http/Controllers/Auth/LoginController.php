<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Only allow Admin, SuperAdmin, and Staff to login
            if (!$user->hasAnyRole(['Admin', 'SuperAdmin', 'Staff'])) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account does not have permission to access this system. Only Admin and Staff can login.'],
                ]);
            }
            
            $request->session()->regenerate();
            return $this->redirectToDashboard();
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    }

    /**
     * Redirect user to their appropriate dashboard.
     */
    protected function redirectToDashboard()
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['Admin', 'SuperAdmin'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Staff')) {
            return redirect()->route('staff.dashboard');
        }

        // If user somehow doesn't have allowed role, logout and redirect to login
        Auth::logout();
        return redirect()->route('login')->with('error', 'You do not have permission to access this system.');
    }
}
