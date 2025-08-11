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
}