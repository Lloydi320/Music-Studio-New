<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'service_type',
        'google_event_id',
    ];

    protected $casts = [
        'duration' => 'integer',
        'date' => 'date',
    ];

    // Add validation rules
    public static function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
            'duration' => 'required|integer|min:1|max:8',
            'status' => 'in:pending,confirmed,cancelled,rejected',
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
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->reference)) {
                $booking->reference = 'BK' . strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Service type constants
    const SERVICE_TYPES = [
        'studio_rental' => 'Studio Rental',
        'recording_session' => 'Recording Session',
        'music_lesson' => 'Music Lesson',
        'band_practice' => 'Band Practice',
        'audio_production' => 'Audio Production',
        'instrument_rental' => 'Instrument Rental',
        'other' => 'Other Services'
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