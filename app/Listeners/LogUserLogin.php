<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the login event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $guard = $event->guard;
        
        $details = "Guard: {$guard}";
        if ($user->isAdmin()) {
            $details .= ' (Admin Access)';
        }
        
        ActivityLog::logAuth(
            ActivityLog::ACTION_LOGIN,
            $user,
            $details
        );
    }
}
