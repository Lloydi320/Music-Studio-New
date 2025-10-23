<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login to access admin panel.');
        }

        // Check if user has admin privileges
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return redirect('/login')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}