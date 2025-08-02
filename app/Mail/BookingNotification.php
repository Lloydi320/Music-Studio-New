<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class BookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $user;
    public $formattedDate;
    public $formattedTime;

    public function __construct(Booking $booking, User $user)
    {
        $this->booking = $booking;
        $this->user = $user;
        
        // Format the date and time for better readability
        $date = Carbon::parse($booking->date);
        $this->formattedDate = $date->format('l, F j, Y'); // e.g., "Monday, January 15, 2024"
        $this->formattedTime = $booking->time_slot;
    }

    public function build()
    {
        return $this->subject('Booking Confirmation - Lemon Hub Studio')
                    ->view('emails.booking-notification')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'bookingReference' => $this->booking->reference,
                        'bookingDate' => $this->formattedDate,
                        'bookingTime' => $this->formattedTime,
                        'bookingDuration' => $this->booking->duration . ' hour' . ($this->booking->duration > 1 ? 's' : ''),
                        'bookingStatus' => ucfirst($this->booking->status),
                        'studioName' => 'Lemon Hub Studio',
                        'studioEmail' => 'magamponr@gmail.com'
                    ]);
    }
}