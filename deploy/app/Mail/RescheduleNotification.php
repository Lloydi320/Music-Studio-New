<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class RescheduleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $rescheduleData;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, array $rescheduleData)
    {
        $this->booking = $booking;
        $this->rescheduleData = $rescheduleData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reschedule Request - ' . $this->booking->reference,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reschedule-notification',
            with: [
                'booking' => $this->booking,
                'rescheduleData' => $this->rescheduleData,
                'studioName' => 'Lemon Hub Studio'
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}