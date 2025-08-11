<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\ActivityLog;

class LogUserLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the logout event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        $guard = $event->guard;
        
        if ($user) {
            $details = "Guard: {$guard}";
            if ($user->isAdmin()) {
                $details .= ' (Admin Session)';
            }
            
            ActivityLog::logAuth(
                ActivityLog::ACTION_LOGOUT,
                $user,
                $details
            );
        }
    }
}
