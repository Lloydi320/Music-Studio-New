<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RehearsalQrConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration_minutes',
        'reservation_fee_php',
        'qr_image_path',
        'enabled',
        'valid_from',
        'valid_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'reservation_fee_php' => 'decimal:2',
    ];
}