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
    
    public function redirectToGoogle()
    {
        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $googleDriver */
            $googleDriver = Socialite::driver('google');
            return $googleDriver
                ->with(['prompt' => 'select_account'])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth redirect error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Google OAuth is not configured properly.');
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
                return redirect('/')->with('error', 'Google login failed. No user information received.');
            }

            // Check if this is a new user
            $existingUser = User::where('email', $googleUser->email)->first();
            $isNewUser = !$existingUser;

            // Create or update user - allows ANY Gmail user to register
            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name ?? 'Google User',
                'google_id' => $googleUser->id,
                'password' => bcrypt(uniqid()),
                'email_verified_at' => now(), // Mark email as verified since it's from Google
            ]);

            // Log the login/registration
            if ($isNewUser) {
                Log::info('New user registered via Google OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'google_id' => $googleUser->id
                ]);
            } else {
                Log::info('Existing user logged in via Google OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email
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
                    'login_time' => now()->toDateTimeString()
                ]
            ]);
            
            $welcomeMessage = $isNewUser 
                ? 'Welcome to Lemon Hub Studio! Your account has been created successfully.' 
                : 'Welcome back to Lemon Hub Studio!';
            
            // Check if user is admin and redirect accordingly
            /** @var User $user */
            if ($user->isAdmin()) {
                return redirect('/admin/dashboard')->with('success', $welcomeMessage . ' Redirected to admin dashboard.');
            }
            
            return redirect('/')->with('success', $welcomeMessage);
            
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Google login failed. Please try again. Error: ' . $e->getMessage());
        }
    }

   
    public function logout(Request $request)
    {
        try {
            // Clear all session data
            $request->session()->flush();
            
            // Logout the user
            Auth::logout();
            
            // Clear Google avatar session
            if (session()->has('google_user_avatar')) {
                session()->forget('google_user_avatar');
            }
            
            // Clear CSRF token and regenerate session
            $request->session()->forget('_token');
            $request->session()->regenerate();
            
            // Clear any Socialite session data
            if (session()->has('socialite.state')) {
                session()->forget('socialite.state');
            }
            
            return redirect('/')->with('success', 'Successfully logged out!');
            
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Logout failed. Please try again.');
        }
    }
}
