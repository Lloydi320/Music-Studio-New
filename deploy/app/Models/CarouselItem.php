<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'expertise',
        'image_path',
        'order_position',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_position' => 'integer'
    ];

    /**
     * Scope to get only active carousel items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get carousel items ordered by position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_position', 'asc');
    }
}
