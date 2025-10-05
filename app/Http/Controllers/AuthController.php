<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('register');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'login_type' => 'sometimes|in:user,admin'
        ]);

        $credentials = $request->only('email', 'password');
        $loginType = $request->get('login_type', 'user');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // For admin login, check if user has admin privileges
            if ($loginType === 'admin' && !$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Admin privileges required.',
                ]);
            }

            $request->session()->regenerate();

            Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'login_type' => $loginType
            ]);

            // Redirect based on user type
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
            } else {
                return redirect('/')->with('success', 'Welcome to Lemon Hub Studio!');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|unique:pending_users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create pending user instead of actual user
        $pendingUser = \App\Models\PendingUser::createPendingUser(
            $request->name,
            $request->email,
            $request->password
        );

        Log::info('New pending user created', [
            'pending_user_id' => $pendingUser->id,
            'email' => $pendingUser->email,
            'name' => $pendingUser->name
        ]);

        // Send email verification for pending user
        \Illuminate\Support\Facades\Mail::to($pendingUser->email)
            ->send(new \App\Mail\PendingUserVerification($pendingUser));

        return redirect()->back()->with('registration_success', 'Registration initiated! Please check your email and click the verification link to complete your account creation. The link expires in 24 hours.');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the email verification notice
     */
    public function showVerifyEmailForm()
    {
        return view('auth.verify-email');
    }

    /**
     * Handle email verification
     */
    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            return redirect('/')->with('error', 'Invalid verification link.');
        }

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect('/')->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('info', 'Your email is already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        Auth::login($user);

        return redirect('/')->with('success', 'Your email has been verified! Welcome to Lemon Hub Studio!');
    }

    /**
     * Handle email verification for pending users
     */
    public function verifyPendingUser(Request $request)
    {
        $token = $request->route('token');
        $email = $request->route('email');

        // Find pending user by token and email
        $pendingUser = \App\Models\PendingUser::where('verification_token', $token)
            ->where('email', $email)
            ->first();

        if (!$pendingUser) {
            return redirect('/')->with('error', 'Invalid or expired verification link. Please try registering again.');
        }

        if ($pendingUser->isTokenExpired()) {
            $pendingUser->delete(); // Clean up expired pending user
            return redirect('/')->with('error', 'Verification link has expired. Please register again.');
        }

        // Check if user already exists (edge case)
        if (User::where('email', $email)->exists()) {
            $pendingUser->delete(); // Clean up pending user
            return redirect('/')->with('info', 'An account with this email already exists. Please log in.');
        }

        // Convert pending user to actual user
        $user = $pendingUser->convertToUser();

        Log::info('User account created via email verification', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name
        ]);

        // Log the user in automatically
        Auth::login($user);

        return redirect('/')->with('success', 'Email verified successfully! Your account has been created and you are now logged in. Welcome to Lemon Hub Studio!');
    }

    /**
     * Resend verification email for pending users
     */
    public function resendPendingVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $pendingUser = \App\Models\PendingUser::where('email', $request->email)->first();

        if (!$pendingUser) {
            return back()->with('error', 'No pending registration found for this email address.');
        }

        if ($pendingUser->isTokenExpired()) {
            $pendingUser->delete();
            return back()->with('error', 'Your registration has expired. Please register again.');
        }

        // Generate new token and send email
        $pendingUser->regenerateToken();
        \Illuminate\Support\Facades\Mail::to($pendingUser->email)
            ->send(new \App\Mail\PendingUserVerification($pendingUser));

        return back()->with('success', 'Verification email resent! Please check your inbox.');
    }

    /**
     * Resend email verification notification
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/')->with('info', 'Your email is already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent! Please check your inbox.');
    }
}
