<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Redirect to Google for OAuth
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    // Handle Google OAuth callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            
            // Find or create user
            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => bcrypt(uniqid()), // Random password for Google users
            ]);

            Auth::login($user);
            // Save Google avatar in session
            session(['google_user_avatar' => $googleUser->avatar]);
            
            return redirect('/')->with('success', 'Successfully logged in with Google!');
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Google login failed. Please try again.');
        }
    }

    // Logout
    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();
        
        // Logout the user
        Auth::logout();
        
        // Clear any Google OAuth tokens from session
        if (session()->has('google_user_avatar')) {
            session()->forget('google_user_avatar');
        }
        
        // Clear any other Google-related session data
        $request->session()->forget('_token');
        $request->session()->regenerate();
        
        // Clear any Socialite session data
        if (session()->has('socialite.state')) {
            session()->forget('socialite.state');
        }
        
        return redirect('/')->with('success', 'Successfully logged out!');
    }
}
