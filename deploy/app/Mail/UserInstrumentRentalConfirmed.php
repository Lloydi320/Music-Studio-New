<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\InstrumentRental;

class UserInstrumentRentalConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public InstrumentRental $rental;

    public function __construct(InstrumentRental $rental)
    {
        $this->rental = $rental;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Instrument Rental Confirmed - ' . ($this->rental->reference ?? 'Rental'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-instrument-rental-confirmed',
            with: [
                'rental' => $this->rental,
                'studioName' => 'Lemon Hub Studio',
                'studioEmail' => config('mail.from.address'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}