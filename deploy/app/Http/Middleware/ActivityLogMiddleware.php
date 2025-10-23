<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log significant user activities
        $this->logActivity($request);
        
        return $next($request);
    }
    
    /**
     * Log user activities based on request
     */
    private function logActivity(Request $request)
    {
        $user = Auth::user();
        $route = $request->route();
        
        if (!$route) {
            return;
        }
        
        $routeName = $route->getName();
        $method = $request->method();
        $path = $request->path();
        
        // Skip logging for certain routes to avoid spam
        $skipRoutes = [
            'api.*',
            '*.css',
            '*.js',
            '*.png',
            '*.jpg',
            '*.jpeg',
            '*.gif',
            '*.svg',
            '*.ico',
            'livewire.*'
        ];
        
        foreach ($skipRoutes as $skipPattern) {
            if (fnmatch($skipPattern, $routeName) || fnmatch($skipPattern, $path)) {
                return;
            }
        }
        
        // Log admin access
        if ($user && $user->isAdmin() && str_starts_with($path, 'admin')) {
            $description = match($routeName) {
                'admin.dashboard' => 'Admin accessed dashboard',
                'admin.bookings' => 'Admin viewed bookings list',
                'admin.calendar' => 'Admin accessed calendar',
                'admin.database' => 'Admin accessed database management',
                'admin.analytics' => 'Admin viewed analytics',
                'admin.activity-logs' => 'Admin viewed activity logs',
                default => "Admin accessed: {$path}"
            };
            
            ActivityLog::logAdmin($description, ActivityLog::ACTION_ADMIN_ACCESS, ActivityLog::SEVERITY_LOW);
        }
        
        // Log critical system operations
        if ($method === 'DELETE' && $user) {
            ActivityLog::logAdmin(
                "User performed DELETE operation on: {$path}",
                ActivityLog::ACTION_SYSTEM_CHANGE,
                ActivityLog::SEVERITY_HIGH
            );
        }
        
        // Log database operations
        if (str_contains($path, 'database') && $user && $user->isAdmin()) {
            ActivityLog::logAdmin(
                "Admin accessed database operations: {$path}",
                ActivityLog::ACTION_DATABASE_OPERATION,
                ActivityLog::SEVERITY_MEDIUM
            );
        }
    }
}
