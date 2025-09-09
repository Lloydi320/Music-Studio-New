<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\InstrumentRental;

class InstrumentRescheduleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $rental;
    public $rescheduleData;

    /**
     * Create a new message instance.
     */
    public function __construct(InstrumentRental $rental, array $rescheduleData)
    {
        $this->rental = $rental;
        $this->rescheduleData = $rescheduleData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Instrument Rental Reschedule Request - ' . $this->rental->reference,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.instrument-reschedule-notification',
            with: [
                'rental' => $this->rental,
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