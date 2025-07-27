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
            return Socialite::driver('google')
                ->with(['prompt' => 'select_account'])
                ->redirect();
        } catch (\Exception $e) {
            \Log::error('Google OAuth redirect error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Google OAuth is not properly configured. Please contact administrator.');
        }
    }

   
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser || !$googleUser->email) {
                Log::error('Google OAuth callback: No user or email received');
                return redirect('/')->with('error', 'Google login failed. No user information received.');
            }

            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name ?? 'Google User',
                'google_id' => $googleUser->id,
                'password' => bcrypt(uniqid()),
            ]);

            Auth::login($user);
          
            if ($googleUser->avatar) {
                session(['google_user_avatar' => $googleUser->avatar]);
            }
            
            return redirect('/')->with('success', 'Successfully logged in with Google!');
            
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
