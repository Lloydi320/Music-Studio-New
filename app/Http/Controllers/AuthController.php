<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    
    public function redirectToGoogle(Request $request)
    {
        try {
            // Store login type in session
            $loginType = $request->get('type', 'user');
            session(['login_type' => $loginType]);
            
            /** @var \Laravel\Socialite\Two\GoogleProvider $googleDriver */
            $googleDriver = Socialite::driver('google');
            return $googleDriver
                ->with(['prompt' => 'select_account'])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth redirect error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Google OAuth is not configured properly.');
        }
    }

   
    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $googleDriver */
            $googleDriver = Socialite::driver('google');
            $googleUser = $googleDriver->stateless()->user();

            if (!$googleUser || !$googleUser->email) {
                Log::error('Google OAuth callback: No user or email received');
                return redirect('/login')->with('error', 'Google login failed. No user information received.');
            }

            // Get login type from session
            $loginType = session('login_type', 'user');
            
            // Check if this is a new user
            $existingUser = User::where('email', $googleUser->email)->first();
            $isNewUser = !$existingUser;

            // For admin login, check if user exists and is admin
            if ($loginType === 'admin') {
                if (!$existingUser) {
                    return redirect('/login')->with('error', 'Admin account not found. Please contact the system administrator.');
                }
                
                if (!$existingUser->isAdmin()) {
                    return redirect('/login')->with('error', 'Access denied. Admin privileges required.');
                }
                
                $user = $existingUser;
            } else {
                // Regular user login - create or update user
                $user = User::updateOrCreate([
                    'email' => $googleUser->email,
                ], [
                    'name' => $googleUser->name ?? 'Google User',
                    'google_id' => $googleUser->id,
                ]);
            }

            // Log the login
            if ($isNewUser && $loginType === 'user') {
                Log::info('New user registered via Google OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]);
            } else {
                Log::info('User logged in via Google OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'login_type' => $loginType
                ]);
            }

            Auth::login($user);
          
            // Store Google avatar and additional info in session
            if ($googleUser->avatar) {
                session(['google_user_avatar' => $googleUser->avatar]);
            }
            
            // Store additional Google user info if available
            session([
                'google_user_info' => [
                    'id' => $googleUser->id,
                    'email' => $googleUser->email,
                    'name' => $googleUser->name,
                    'avatar' => $googleUser->avatar,
                    'login_time' => now()->toDateTimeString(),
                    'login_type' => $loginType
                ]
            ]);
            
            // Clear login type from session
            session()->forget('login_type');
            
            // Redirect based on user type
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
            } else {
                return redirect('/')->with('success', 'Welcome to Lemon Hub Studio!');
            }
            
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Login failed. Please try again.');
        }
    }

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
}
