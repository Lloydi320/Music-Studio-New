<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_name',
        'user_role',
        'description',
        'ip_address',
        'user_agent',
        'action_type',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create a new activity log entry
     */
    public static function logActivity($description, $actionType = null, $userId = null)
    {
        $user = Auth::user();
        
        return self::create([
            'user_name' => $user ? $user->name : 'Guest',
            'user_role' => $user && $user->role === 'admin' ? 'Admin' : 'Customer',
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'action_type' => $actionType,
            'user_id' => $userId ?? ($user ? $user->id : null)
        ]);
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y H:i A');
    }
}
