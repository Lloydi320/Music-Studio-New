<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

   
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            
          
            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => bcrypt(uniqid()),
            ]);

            Auth::login($user);
          
            session(['google_user_avatar' => $googleUser->avatar]);
            
            return redirect('/')->with('success', 'Successfully logged in with Google!');
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Google login failed. Please try again.');
        }
    }

   
    public function logout(Request $request)
    {
        
        $request->session()->flush();
        
        
        Auth::logout();
        
        
        if (session()->has('google_user_avatar')) {
            session()->forget('google_user_avatar');
        }
        
     
        $request->session()->forget('_token');
        $request->session()->regenerate();
        
        // Clear any Socialite session data
        if (session()->has('socialite.state')) {
            session()->forget('socialite.state');
        }
        
        return redirect('/')->with('success', 'Successfully logged out!');
    }
}
