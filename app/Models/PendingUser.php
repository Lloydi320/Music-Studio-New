<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PendingUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_token',
        'token_expires_at',
    ];

    protected $hidden = [
        'password',
        'verification_token',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Create a new pending user with verification token
     */
    public static function createPendingUser($name, $email, $password)
    {
        return self::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'verification_token' => Str::random(64),
            'token_expires_at' => Carbon::now()->addHours(24), // Token expires in 24 hours
        ]);
    }

    /**
     * Check if the verification token is expired
     */
    public function isTokenExpired()
    {
        return Carbon::now()->isAfter($this->token_expires_at);
    }

    /**
     * Convert pending user to actual user
     */
    public function convertToUser()
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password, // Already hashed
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        // Delete the pending user record
        $this->delete();

        return $user;
    }

    /**
     * Generate a new verification token
     */
    public function regenerateToken()
    {
        $this->update([
            'verification_token' => Str::random(64),
            'token_expires_at' => Carbon::now()->addHours(24),
        ]);
    }
}