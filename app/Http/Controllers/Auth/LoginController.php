<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureAccess;
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
            return EnsureAccess::redirectToDashboard();
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

            // Check if user has at least one dashboard permission
            if (!$user->can('view admin dashboard') &&
                !$user->can('view staff dashboard') &&
                !$user->can('view client dashboard') &&
                !$user->can('view lead dashboard')) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account does not have permission to access this system.'],
                ]);
            }

            $request->session()->regenerate();
            return EnsureAccess::redirectToDashboard();
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
}
