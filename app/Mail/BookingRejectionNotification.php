<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class BookingRejectionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $formattedDate;
    public $formattedTime;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $date = Carbon::parse($booking->date);
        $this->formattedDate = $date->format('l, F j, Y');
        $this->formattedTime = $booking->time_slot;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $user = $this->booking->user; // Expect relationship to be loaded

        return $this->subject('Booking Rejected - Lemon Hub Studio')
            ->view('emails.booking-rejected')
            ->with([
                'userName' => $user->name ?? 'Customer',
                'userEmail' => $user->email ?? 'N/A',
                'bookingReference' => $this->booking->reference,
                'bookingDate' => $this->formattedDate,
                'bookingTime' => $this->formattedTime,
                'bookingDuration' => $this->booking->duration . ' hour' . ($this->booking->duration > 1 ? 's' : ''),
                'studioName' => 'Lemon Hub Studio',
                'studioEmail' => 'magamponr@gmail.com'
            ]);
    }
}