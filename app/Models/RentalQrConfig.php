<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalQrConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_type',
        'reservation_fee_php',
        'qr_image_path',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'reservation_fee_php' => 'decimal:2',
    ];
}