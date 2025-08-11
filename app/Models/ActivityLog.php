<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_name',
        'user_role',
        'description',
        'ip_address',
        'user_agent',
        'action_type',
        'user_id',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'severity_level',
        'session_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    // Action type constants
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_BOOKING_CREATED = 'booking_created';
    const ACTION_BOOKING_UPDATED = 'booking_updated';
    const ACTION_BOOKING_CANCELLED = 'booking_cancelled';
    const ACTION_BOOKING_APPROVED = 'booking_approved';
    const ACTION_BOOKING_REJECTED = 'booking_rejected';
    const ACTION_BOOKING_DELETED = 'booking_deleted';
    const ACTION_RENTAL_CREATED = 'rental_created';
    const ACTION_RENTAL_UPDATED = 'rental_updated';
    const ACTION_RENTAL_CANCELLED = 'rental_cancelled';
    const ACTION_USER_CREATED = 'user_created';
    const ACTION_USER_UPDATED = 'user_updated';
    const ACTION_USER_DELETED = 'user_deleted';
    const ACTION_ADMIN_ACCESS = 'admin_access';
    const ACTION_DATABASE_OPERATION = 'database_operation';
    const ACTION_CALENDAR_SYNC = 'calendar_sync';
    const ACTION_SYSTEM_CHANGE = 'system_change';

    // Severity levels
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Relationship with User model
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a comprehensive activity log entry
     */
    public static function logActivity(
        string $description, 
        string $actionType = null, 
        int $userId = null,
        string $resourceType = null,
        int $resourceId = null,
        array $oldValues = null,
        array $newValues = null,
        string $severityLevel = self::SEVERITY_LOW
    ) {
        $user = Auth::user();
        
        return self::create([
            'user_name' => $user ? $user->name : 'Guest',
            'user_role' => $user && $user->isAdmin() ? 'Admin' : 'Customer',
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'action_type' => $actionType,
            'user_id' => $userId ?? ($user ? $user->id : null),
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'severity_level' => $severityLevel,
            'session_id' => session()->getId()
        ]);
    }

    /**
     * Log authentication events
     */
    public static function logAuth(string $action, User $user = null, string $details = '')
    {
        $description = match($action) {
            self::ACTION_LOGIN => 'User logged in' . ($details ? ': ' . $details : ''),
            self::ACTION_LOGOUT => 'User logged out' . ($details ? ': ' . $details : ''),
            default => $action . ($details ? ': ' . $details : '')
        };

        return self::logActivity(
            $description,
            $action,
            $user?->id,
            'User',
            $user?->id,
            null,
            null,
            self::SEVERITY_MEDIUM
        );
    }

    /**
     * Log booking-related events
     */
    public static function logBooking(string $action, Booking $booking, array $oldValues = null, array $newValues = null)
    {
        $description = match($action) {
            self::ACTION_BOOKING_CREATED => "Booking created: {$booking->reference}",
            self::ACTION_BOOKING_UPDATED => "Booking updated: {$booking->reference}",
            self::ACTION_BOOKING_CANCELLED => "Booking cancelled: {$booking->reference}",
            self::ACTION_BOOKING_APPROVED => "Booking approved: {$booking->reference}",
            self::ACTION_BOOKING_REJECTED => "Booking rejected: {$booking->reference}",
            self::ACTION_BOOKING_DELETED => "Booking deleted: {$booking->reference}",
            default => $action . ": {$booking->reference}"
        };

        $severity = match($action) {
            self::ACTION_BOOKING_DELETED => self::SEVERITY_HIGH,
            self::ACTION_BOOKING_CANCELLED, self::ACTION_BOOKING_REJECTED => self::SEVERITY_MEDIUM,
            default => self::SEVERITY_LOW
        };

        return self::logActivity(
            $description,
            $action,
            null,
            'Booking',
            $booking->id,
            $oldValues,
            $newValues,
            $severity
        );
    }

    /**
     * Log instrument rental events
     */
    public static function logRental(string $action, InstrumentRental $rental, array $oldValues = null, array $newValues = null)
    {
        $description = match($action) {
            self::ACTION_RENTAL_CREATED => "Instrument rental created: {$rental->reference}",
            self::ACTION_RENTAL_UPDATED => "Instrument rental updated: {$rental->reference}",
            self::ACTION_RENTAL_CANCELLED => "Instrument rental cancelled: {$rental->reference}",
            default => $action . ": {$rental->reference}"
        };

        $severity = match($action) {
            self::ACTION_RENTAL_CANCELLED => self::SEVERITY_MEDIUM,
            default => self::SEVERITY_LOW
        };

        return self::logActivity(
            $description,
            $action,
            null,
            'InstrumentRental',
            $rental->id,
            $oldValues,
            $newValues,
            $severity
        );
    }

    /**
     * Log admin actions
     */
    public static function logAdmin(string $description, string $actionType = self::ACTION_ADMIN_ACCESS, string $severity = self::SEVERITY_MEDIUM)
    {
        return self::logActivity(
            $description,
            $actionType,
            null,
            null,
            null,
            null,
            null,
            $severity
        );
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y H:i A');
    }

    /**
     * Get severity badge class
     */
    public function getSeverityBadgeClassAttribute()
    {
        return match($this->severity_level) {
            self::SEVERITY_CRITICAL => 'badge-critical',
            self::SEVERITY_HIGH => 'badge-high',
            self::SEVERITY_MEDIUM => 'badge-medium',
            default => 'badge-low'
        };
    }

    /**
     * Get action type display name
     */
    public function getActionTypeDisplayAttribute()
    {
        return match($this->action_type) {
            self::ACTION_LOGIN => 'Login',
            self::ACTION_LOGOUT => 'Logout',
            self::ACTION_BOOKING_CREATED => 'Booking Created',
            self::ACTION_BOOKING_UPDATED => 'Booking Updated',
            self::ACTION_BOOKING_CANCELLED => 'Booking Cancelled',
            self::ACTION_BOOKING_APPROVED => 'Booking Approved',
            self::ACTION_BOOKING_REJECTED => 'Booking Rejected',
            self::ACTION_BOOKING_DELETED => 'Booking Deleted',
            self::ACTION_RENTAL_CREATED => 'Rental Created',
            self::ACTION_RENTAL_UPDATED => 'Rental Updated',
            self::ACTION_RENTAL_CANCELLED => 'Rental Cancelled',
            self::ACTION_USER_CREATED => 'User Created',
            self::ACTION_USER_UPDATED => 'User Updated',
            self::ACTION_USER_DELETED => 'User Deleted',
            self::ACTION_ADMIN_ACCESS => 'Admin Access',
            self::ACTION_DATABASE_OPERATION => 'Database Operation',
            self::ACTION_CALENDAR_SYNC => 'Calendar Sync',
            self::ACTION_SYSTEM_CHANGE => 'System Change',
            default => ucfirst(str_replace('_', ' ', $this->action_type ?? 'Unknown'))
        };
    }
}
