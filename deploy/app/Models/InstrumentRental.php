<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\InstrumentRentalDeclinedNotification;

class InstrumentRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instrument_type',
        'instrument_name',
        'rental_start_date',
        'rental_end_date',
        'rental_duration_days',
        'daily_rate',
        'total_amount',
        'status',
        'reschedule_source',
        'reference',
        'four_digit_code',
        'payment_reference',
        'notes',
        'receipt_image',
        'pickup_location',
        'return_location',
        'transportation',
        'delivery_time',
        'id_provided',
        'venue_type',
        'event_duration_hours',
        'documentation_consent',
        'reservation_fee',
        'security_deposit',
        'name',
        'email',
        'phone',
    ];

    protected $casts = [
        'rental_duration_days' => 'integer',
        'daily_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'rental_start_date' => 'date',
        'rental_end_date' => 'date',
        'id_provided' => 'boolean',
        'event_duration_hours' => 'integer',
        'documentation_consent' => 'boolean',
        'reservation_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rental) {
            if (empty($rental->reference)) {
                $rental->reference = self::generateUniqueReference();
            }
        });

        static::updated(function ($rental) {
            // If status changes to 'declined', send notification email
            if ($rental->isDirty('status') && $rental->status === 'declined') {
                try {
                    
                    $user = $rental->user;
                    if ($user) {
                        \Mail::to($user->email)->send(new \App\Mail\RentalDeclineNotification($rental));
                    }
                } catch (\Throwable $e) {
                    \Log::error('Failed to send RentalDeclineNotification: ' . $e->getMessage());
                }
            }
        });

        static::created(function ($rental) {
            try {
                app(\App\Services\IcsGenerator::class)->regenerate();
            } catch (\Throwable $e) {
                \Log::warning('ICS regenerate failed after rental create: ' . $e->getMessage());
            }
        });
    }

    /**
     * Generate a unique instrument rental reference
     * Format: IR-YYYY-XXXXXX (e.g., IR-2025-A1B2C3)
     */
    public static function generateUniqueReference()
    {
        $year = Carbon::now()->year;
        $maxAttempts = 100;
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            // Generate 6-character alphanumeric code
            $code = strtoupper(Str::random(6));
            $reference = "IR-{$year}-{$code}";
            
            // Check if reference already exists
            if (!self::where('reference', $reference)->exists()) {
                return $reference;
            }
        }
        
        // Fallback with timestamp if all attempts fail
        $timestamp = Carbon::now()->format('His');
        return "IR-{$year}-{$timestamp}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Available instrument types
    public static function getInstrumentTypes()
    {
        return [
            'drums' => 'Drum Set',
            'guitar_amp' => 'Guitar Amplifier',
            'bass_amp' => 'Bass Amplifier',
            'keyboard_amp' => 'Keyboard/Acoustic Guitar Amp',
            'keyboard' => 'Keyboard',
            'guitar' => 'Electric Guitar',
            'bass' => 'Bass Guitar',
            'cables' => 'Cables & Accessories',
        ];
    }

    // Available instruments for each type
    public static function getAvailableInstruments()
    {
        return [
            'drums' => [
                'drum_set_yamaha' => 'DRUM SET - YAMAHA MANU KATCHE (JUNGLE KIT)',
            ],
            'guitar_amp' => [
                'fender_champion_100' => 'GUITAR AMP - FENDER CHAMPION 100',
                'peavey_bandit' => 'GUITAR AMP - PEAVEY BANDIT 80/100',
            ],
            'bass_amp' => [
                'fender_rumble_100' => 'BASS AMP - FENDER RUMBLE 100',
            ],
            'keyboard_amp' => [
                'avatar_dm50' => 'KEYBOARD AMP/ACOUSTIC GUITAR AMP - AVATAR DM50',
            ],
            'keyboard' => [
                'roland_gokeys' => 'KEYBOARD - ROLAND GO:KEYS (GO-61K)',
            ],
            'guitar' => [
                'electric_guitar' => 'ELECTRIC GUITAR',
            ],
            'bass' => [
                'bass_guitar' => 'BASS GUITAR',
            ],
            'cables' => [
                'guitar_cable' => 'GUITAR CABLE',
            ],
        ];
    }

    // Daily rates for each instrument type (matching the flyer)
    public static function getDailyRates()
    {
        return [
            'drums' => 1500.00,
            'guitar_amp' => 900.00,
            'bass_amp' => 900.00,
            'keyboard_amp' => 750.00,
            'keyboard' => 750.00,
            'guitar' => 500.00,
            'bass' => 550.00,
            'cables' => 50.00,
        ];
    }

    protected static function booted()
    {
        static::updated(function (InstrumentRental $rental) {
            if ($rental->wasChanged('status')) {
                $status = strtolower((string) $rental->status);
                if (in_array($status, ['cancelled', 'rejected'])) {
                    try {
                        $email = optional($rental->user)->email ?? ($rental->email ?? null);
                        if ($email) {
                            Mail::to($email)->send(new InstrumentRentalDeclinedNotification($rental));
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send rental declined email', [
                            'rental_id' => $rental->id,
                            'reference' => $rental->reference,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        });
    }
}
