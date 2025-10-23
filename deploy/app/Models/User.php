<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Feedback;
use App\Models\Booking;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'google_calendar_token',
        'google_calendar_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function hasGoogleCalendarAccess(): bool
    {
        return !empty($this->google_calendar_token);
    }

    // Add this method to the User class
    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/|confirmed',
            'is_admin' => 'boolean',
            'google_calendar_token' => 'nullable|json',
            'google_calendar_id' => 'nullable|string',
        ];
    }

    public static function updateRules($id = null)
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/|confirmed',
            'is_admin' => 'sometimes|boolean',
            'google_calendar_token' => 'nullable|json',
            'google_calendar_id' => 'nullable|string',
        ];
    }
}
