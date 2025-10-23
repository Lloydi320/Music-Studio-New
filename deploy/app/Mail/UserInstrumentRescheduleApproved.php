<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\InstrumentRental;

class UserInstrumentRescheduleApproved extends Mailable
{
    use Queueable, SerializesModels;

    public InstrumentRental $rental;
    public array $previousData;

    public function __construct(InstrumentRental $rental, array $previousData = [])
    {
        $this->rental = $rental;
        $this->previousData = $previousData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reschedule Approved - ' . ($this->rental->reference ?? 'Instrument Rental'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-instrument-reschedule-approved',
            with: [
                'rental' => $this->rental,
                'previousData' => $this->previousData,
                'studioName' => 'Lemon Hub Studio',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}