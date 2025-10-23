<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_slot',
        'duration',
        'price',
        'total_amount',
        'reference',
        'status',
        'is_walk_in_booking',
        'created_by_admin_id',
        'reschedule_source',
        'service_type',
        'google_event_id',
        'band_name',
        'email',
        'contact_number',
        'reference_code',
        'image_path',
        'lesson_type',
    ];

    protected $casts = [
        'duration' => 'integer',
        'date' => 'date',
        'is_walk_in_booking' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->reference)) {
                $booking->reference = self::generateUniqueReference();
            }
        });

        static::created(function ($booking) {
            try {
                app(\App\Services\IcsGenerator::class)->regenerate();
            } catch (\Throwable $e) {
                \Log::warning('ICS regenerate failed after booking create: ' . $e->getMessage());
            }
        });
    }

    /**
     * Generate a unique booking reference
     * Format: BK-YYYY-XXXXXX (e.g., BK-2025-A1B2C3)
     */
    public static function generateUniqueReference()
    {
        $year = Carbon::now()->year;
        $maxAttempts = 100;
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            // Generate 6-character alphanumeric code
            $code = strtoupper(Str::random(6));
            $reference = "BK-{$year}-{$code}";
            
            // Check if reference already exists
            if (!self::where('reference', $reference)->exists()) {
                return $reference;
            }
        }
        
        // Fallback with timestamp if all attempts fail
        $timestamp = Carbon::now()->format('His');
        return "BK-{$year}-{$timestamp}";
    }

    // Add validation rules
    public static function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
            'duration' => 'required|integer|min:1|max:8',
            'status' => 'in:pending,confirmed,cancelled,rejected',
            'band_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'reference_code' => 'nullable|string|regex:/^[0-9]{13}$/|unique:bookings,reference_code',
            'image_path' => 'nullable|string|max:500',
        ];
    }

    // Add validation for updates
    public static function updateRules($id = null)
    {
        return [
            'date' => 'sometimes|date|after_or_equal:today',
            'time_slot' => 'sometimes|string',
            'duration' => 'sometimes|integer|min:1|max:8',
            'status' => 'sometimes|in:pending,confirmed,cancelled,rejected',
            'band_name' => 'sometimes|nullable|string|max:255',
            'contact_number' => 'sometimes|nullable|string|max:20',
            'reference_code' => 'sometimes|nullable|string|regex:/^[0-9]{13}$/|unique:bookings,reference_code,' . $id,
            'image_path' => 'sometimes|nullable|string|max:500',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Generate unique 4-digit reference code
    public static function generateReferenceCode()
    {
        do {
            $code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('reference_code', $code)->exists());
        
        return $code;
    }

    // Service type constants
    const SERVICE_TYPES = [
        'studio_rental' => 'Band Rehearsal',
        'instrument_rental' => 'Instrument Rental',
        'solo_rehearsal' => 'Solo Rehearsal',
        'music_lesson' => 'Music Lesson'
    ];

    // Get service type options
    public static function getServiceTypes()
    {
        return self::SERVICE_TYPES;
    }

    // Get service type label
    public function getServiceTypeLabel()
    {
        return self::SERVICE_TYPES[$this->service_type] ?? 'Unknown';
    }

    // Scope for filtering by service type
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    // Get analytics data for service types with optional date filtering
    public static function getServiceTypeAnalytics($startDate = null, $endDate = null)
    {
        $flagColumn = \Schema::hasColumn('bookings', 'is_walk_in_booking')
            ? 'is_walk_in_booking'
            : (\Schema::hasColumn('bookings', 'is_admin_walkin') ? 'is_admin_walkin' : null);

        $analytics = [];
        
        // Updated pricing structure based on actual system pricing
        $studioHourlyRates = [
            1 => 100, 2 => 200, 3 => 300, 4 => 400, 
            5 => 500, 6 => 600, 7 => 700, 8 => 800
        ];
        
        // Define the service types to analyze - only existing types
        $serviceTypes = ['studio_rental', 'solo_rehearsal'];
        
        foreach ($serviceTypes as $key) {
            $label = self::SERVICE_TYPES[$key];
            $totalQuery = self::where('service_type', $key);
            $confirmedQuery = self::where('service_type', $key)->where('status', 'confirmed');
            $pendingQuery = self::where('service_type', $key)->where('status', 'pending');
            $cancelledQuery = self::where('service_type', $key)->where('status', 'cancelled');
            
            // Apply date filtering if provided
            if ($startDate && $endDate) {
                $totalQuery->whereBetween('date', [$startDate, $endDate]);
                $confirmedQuery->whereBetween('date', [$startDate, $endDate]);
                $pendingQuery->whereBetween('date', [$startDate, $endDate]);
                $cancelledQuery->whereBetween('date', [$startDate, $endDate]);
            }
            
            // Exclude walk-in bookings from regular analytics if flag exists
            if ($flagColumn) {
                $totalQuery->where($flagColumn, false);
                $confirmedQuery->where($flagColumn, false);
                $pendingQuery->where($flagColumn, false);
                $cancelledQuery->where($flagColumn, false);
            }

            // Calculate revenue based on service type
            $revenue = 0;
            if (in_array($key, ['studio_rental', 'solo_rehearsal'])) {
                // For studio bookings, use actual hourly rates based on duration
                $confirmedBookings = $confirmedQuery->get(['duration', 'total_amount']);
                $revenue = $confirmedBookings->sum(function ($booking) use ($studioHourlyRates) {
                    // Use total_amount if available, otherwise calculate from duration
                    if ($booking->total_amount && $booking->total_amount > 0) {
                        return $booking->total_amount;
                    }
                    return $studioHourlyRates[$booking->duration] ?? 0;
                });
            } elseif ($key === 'music_lesson') {
                // For music lessons, use total_amount or default lesson rate
                $revenue = $confirmedQuery->sum('total_amount') ?? 0;
                if ($revenue == 0) {
                    // Default lesson rate if total_amount is not set
                    $revenue = $confirmedQuery->count() * 500; // 500 per lesson
                }
            } else {
                // For other services, use total_amount
                $revenue = $confirmedQuery->sum('total_amount') ?? 0;
            }

            $analytics[$key] = [
                'label' => $label,
                'total' => $totalQuery->count(),
                'confirmed' => $confirmedQuery->count(),
                'pending' => $pendingQuery->count(),
                'cancelled' => $cancelledQuery->count(),
                'revenue' => $revenue
            ];
        }
        return $analytics;
    }

    // Get total revenue for a specific service type
    public static function getServiceTypeRevenue($serviceType, $status = 'confirmed')
    {
        $flagColumn = \Schema::hasColumn('bookings', 'is_walk_in_booking')
            ? 'is_walk_in_booking'
            : (\Schema::hasColumn('bookings', 'is_admin_walkin') ? 'is_admin_walkin' : null);

        $query = self::where('service_type', $serviceType)->where('status', $status);
        
        if ($flagColumn) {
            $query->where($flagColumn, false);
        }

        $studioHourlyRates = [
            1 => 100, 2 => 200, 3 => 300, 4 => 400, 
            5 => 500, 6 => 600, 7 => 700, 8 => 800
        ];

        if (in_array($serviceType, ['studio_rental', 'solo_rehearsal'])) {
            $bookings = $query->get(['duration', 'total_amount']);
            return $bookings->sum(function ($booking) use ($studioHourlyRates) {
                if ($booking->total_amount && $booking->total_amount > 0) {
                    return $booking->total_amount;
                }
                return $studioHourlyRates[$booking->duration] ?? 0;
            });
        } elseif ($serviceType === 'music_lesson') {
            $revenue = $query->sum('total_amount') ?? 0;
            if ($revenue == 0) {
                $revenue = $query->count() * 500; // Default lesson rate
            }
            return $revenue;
        } else {
            return $query->sum('total_amount') ?? 0;
        }
    }
}