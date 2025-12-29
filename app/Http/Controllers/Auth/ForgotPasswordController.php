<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to user's email
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Expires in 15 minutes
        $expiresAt = Carbon::now()->addMinutes(15);

        // Invalidate previous OTPs for this email
        PasswordReset::where('email', $email)
            ->where('used', false)
            ->update(['used' => true]);

        // Create new OTP record
        PasswordReset::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'used' => false,
        ]);

        // Send OTP email
        try {
            Mail::to($email)->send(new OtpMail($otp));

            return redirect()->route('password.verify-otp')
                ->with('success', 'OTP has been sent to your email address.')
                ->with('email', $email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send OTP. Please try again.');
        }
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtpForm()
    {
        if (!session('email')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $email = session('email');
        $otp = $request->otp;

        if (!$email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Session expired. Please request a new OTP.');
        }

        // Find valid OTP
        $passwordReset = PasswordReset::where('email', $email)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$passwordReset) {
            return back()->with('error', 'Invalid or expired OTP. Please try again.');
        }

        // Mark OTP as used
        $passwordReset->markAsUsed();

        // Store email in session for password reset
        session(['verified_email' => $email]);

        return redirect()->route('password.reset')
            ->with('success', 'OTP verified successfully. Please set your new password.');
    }

    /**
     * Show password reset form
     */
    public function showResetPasswordForm()
    {
        if (!session('verified_email')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Session expired. Please start again.');
        }

        return view('auth.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $email = session('verified_email');

        if (!$email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Session expired. Please start again.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.forgot')
                ->with('error', 'User not found.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear session
        session()->forget(['email', 'verified_email']);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }
}
