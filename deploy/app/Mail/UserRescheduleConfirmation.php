<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class UserRescheduleConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $rescheduleData;

    public function __construct(Booking $booking, array $rescheduleData)
    {
        $this->booking = $booking;
        $this->rescheduleData = $rescheduleData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your reschedule request - ' . $this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-reschedule-confirmation',
            with: [
                'booking' => $this->booking,
                'rescheduleData' => $this->rescheduleData,
                'studioName' => 'Lemon Hub Studio',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}