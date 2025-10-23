<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class RescheduleRequest extends Model
{
    protected $fillable = [
        'reference',
        'resource_type',
        'resource_id',
        'user_id',
        'customer_name',
        'customer_email',
        'original_data',
        'requested_data',
        'original_date',
        'original_time_slot',
        'original_duration',
        'requested_date',
        'requested_time_slot',
        'requested_duration',
        'original_start_date',
        'original_end_date',
        'requested_start_date',
        'requested_end_date',
        'reason',
        'status',
        'priority',
        'has_conflict',
        'conflict_details',
        'handled_by',
        'handled_at',
        'admin_notes',
        'rejection_reason',
        'customer_notified',
        'admin_notified',
        'customer_notified_at',
        'admin_notified_at',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'original_data' => 'json',
        'requested_data' => 'json',
        'conflict_details' => 'json',
        'metadata' => 'json',
        'has_conflict' => 'boolean',
        'customer_notified' => 'boolean',
        'admin_notified' => 'boolean',
        'handled_at' => 'datetime',
        'customer_notified_at' => 'datetime',
        'admin_notified_at' => 'datetime',
        'original_date' => 'date',
        'requested_date' => 'date',
        'original_start_date' => 'date',
        'original_end_date' => 'date',
        'requested_start_date' => 'date',
        'requested_end_date' => 'date',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Resource type constants
    const RESOURCE_BOOKING = 'booking';
    const RESOURCE_INSTRUMENT_RENTAL = 'instrument_rental';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reference)) {
                $model->reference = $model->generateReference();
            }
            if (empty($model->ip_address)) {
                $model->ip_address = Request::ip();
            }
            if (empty($model->user_agent)) {
                $model->user_agent = Request::userAgent();
            }
        });
    }

    /**
     * Generate a unique reference for the reschedule request
     */
    public function generateReference(): string
    {
        do {
            $reference = 'RSC-' . strtoupper(Str::random(8));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Relationship with User model (customer)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with User model (admin who handled the request)
     */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Polymorphic relationship to the resource being rescheduled
     */
    public function resource(): MorphTo
    {
        return $this->morphTo('resource', 'resource_type', 'resource_id');
    }

    /**
     * Get the booking if resource is a booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'resource_id')
                    ->where('resource_type', self::RESOURCE_BOOKING);
    }

    /**
     * Get the instrument rental if resource is an instrument rental
     */
    public function instrumentRental(): BelongsTo
    {
        return $this->belongsTo(InstrumentRental::class, 'resource_id')
                    ->where('resource_type', self::RESOURCE_INSTRUMENT_RENTAL);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for requests with conflicts
     */
    public function scopeWithConflicts($query)
    {
        return $query->where('has_conflict', true);
    }

    /**
     * Scope for studio booking reschedules
     */
    public function scopeStudioBookings($query)
    {
        return $query->where('resource_type', self::RESOURCE_BOOKING);
    }

    /**
     * Scope for instrument rental reschedules
     */
    public function scopeInstrumentRentals($query)
    {
        return $query->where('resource_type', self::RESOURCE_INSTRUMENT_RENTAL);
    }

    /**
     * Check if this is a studio booking reschedule
     */
    public function isStudioBooking(): bool
    {
        return $this->resource_type === self::RESOURCE_BOOKING;
    }

    /**
     * Check if this is an instrument rental reschedule
     */
    public function isInstrumentRental(): bool
    {
        return $this->resource_type === self::RESOURCE_INSTRUMENT_RENTAL;
    }

    /**
     * Check if the request is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Mark the request as approved
     */
    public function approve(User $admin, string $notes = null): bool
    {
        $this->status = self::STATUS_APPROVED;
        $this->handled_by = $admin->id;
        $this->handled_at = now();
        if ($notes) {
            $this->admin_notes = $notes;
        }
        return $this->save();
    }

    /**
     * Mark the request as rejected
     */
    public function reject(User $admin, string $reason, string $notes = null): bool
    {
        $this->status = self::STATUS_REJECTED;
        $this->handled_by = $admin->id;
        $this->handled_at = now();
        $this->rejection_reason = $reason;
        if ($notes) {
            $this->admin_notes = $notes;
        }
        return $this->save();
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_REJECTED => 'badge-danger',
            self::STATUS_CANCELLED => 'badge-secondary',
            default => 'badge-light'
        };
    }

    /**
     * Get priority badge class for UI
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'badge-danger',
            self::PRIORITY_HIGH => 'badge-warning',
            self::PRIORITY_MEDIUM => 'badge-info',
            self::PRIORITY_LOW => 'badge-secondary',
            default => 'badge-light'
        };
    }

    /**
     * Get formatted resource type for display
     */
    public function getResourceTypeDisplayAttribute(): string
    {
        return match($this->resource_type) {
            self::RESOURCE_BOOKING => 'Studio Booking',
            self::RESOURCE_INSTRUMENT_RENTAL => 'Instrument Rental',
            default => $this->resource_type
        };
    }

    /**
     * Create a reschedule request from activity log data
     */
    public static function createFromActivityLog(ActivityLog $activityLog): self
    {
        $rescheduleData = $activityLog->new_values ?? [];
        $originalData = $activityLog->old_values ?? [];
        
        $request = new self();
        $request->resource_type = $activityLog->resource_type;
        $request->resource_id = $activityLog->resource_id;
        $request->user_id = $activityLog->user_id;
        $request->original_data = $originalData;
        $request->requested_data = $rescheduleData;
        
        // Set customer information
        if ($activityLog->resource_type === self::RESOURCE_BOOKING) {
            $booking = Booking::find($activityLog->resource_id);
            if ($booking) {
                $request->customer_name = $booking->customer_name;
                $request->customer_email = $booking->customer_email;
                $request->original_date = $booking->date;
                $request->original_time_slot = $booking->time_slot;
                $request->original_duration = $booking->duration;
                $request->requested_date = $rescheduleData['new_date'] ?? null;
                $request->requested_time_slot = $rescheduleData['new_time_slot'] ?? null;
                $request->requested_duration = $rescheduleData['new_duration'] ?? null;
            }
        } elseif ($activityLog->resource_type === self::RESOURCE_INSTRUMENT_RENTAL) {
            $rental = InstrumentRental::find($activityLog->resource_id);
            if ($rental) {
                $request->customer_name = $rental->customer_name;
                $request->customer_email = $rental->customer_email;
                $request->original_start_date = $rental->start_date;
                $request->original_end_date = $rental->end_date;
                $request->requested_start_date = $rescheduleData['new_start_date'] ?? null;
                $request->requested_end_date = $rescheduleData['new_end_date'] ?? null;
            }
        }
        
        $request->reason = $rescheduleData['reason'] ?? null;
        $request->created_at = $activityLog->created_at;
        $request->updated_at = $activityLog->updated_at;
        
        return $request;
    }
}