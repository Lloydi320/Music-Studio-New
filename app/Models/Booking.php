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
        'reschedule_source',
        'service_type',
        'google_event_id',
        'band_name',
        'email',
        'contact_number',
        'reference_code',
        'image_path',
    ];

    protected $casts = [
        'duration' => 'integer',
        'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->reference)) {
                $booking->reference = self::generateUniqueReference();
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
            'reference_code' => 'nullable|string|size:4|unique:bookings,reference_code',
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
            'reference_code' => 'sometimes|nullable|string|size:4|unique:bookings,reference_code,' . $id,
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
        'studio_rental' => 'Studio Rental',
        'instrument_rental' => 'Instrument Rental',
        'solo_rehearsal' => 'Solo Rehearsal'
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

    // Get analytics data for service types
    public static function getServiceTypeAnalytics()
    {
        $analytics = [];
        foreach (self::SERVICE_TYPES as $key => $label) {
            $analytics[$key] = [
                'label' => $label,
                'total' => self::where('service_type', $key)->count(),
                'confirmed' => self::where('service_type', $key)->where('status', 'confirmed')->count(),
                'pending' => self::where('service_type', $key)->where('status', 'pending')->count(),
                'revenue' => self::where('service_type', $key)->where('status', 'confirmed')->sum('total_amount') ?? 0
            ];
        }
        return $analytics;
    }
}