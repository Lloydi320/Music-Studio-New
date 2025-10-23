<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\InstrumentRental;

class UserInstrumentRescheduleConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $rental;
    public $rescheduleData;

    public function __construct(InstrumentRental $rental, array $rescheduleData)
    {
        $this->rental = $rental;
        $this->rescheduleData = $rescheduleData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your reschedule request - ' . $this->rental->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-instrument-reschedule-confirmation',
            with: [
                'rental' => $this->rental,
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