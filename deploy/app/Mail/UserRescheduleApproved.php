<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class UserRescheduleApproved extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public array $previousData;

    public function __construct(Booking $booking, array $previousData = [])
    {
        $this->booking = $booking;
        $this->previousData = $previousData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reschedule Approved - ' . ($this->booking->reference ?? 'Booking'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-reschedule-approved',
            with: [
                'booking' => $this->booking,
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