<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PendingUser;
use Illuminate\Support\Facades\URL;

class PendingUserVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $pendingUser;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(PendingUser $pendingUser)
    {
        $this->pendingUser = $pendingUser;
        $this->verificationUrl = URL::temporarySignedRoute(
            'verification.verify.pending',
            now()->addHours(24),
            [
                'token' => $pendingUser->verification_token,
                'email' => $pendingUser->email
            ]
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email Address - Lemon Hub Studio',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pending-user-verification',
            with: [
                'pendingUser' => $this->pendingUser,
                'verificationUrl' => $this->verificationUrl,
            ],
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