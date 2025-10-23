<?php

namespace App\Mail;

use App\Models\InstrumentRental;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class InstrumentRentalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $rental;
    public $formattedStartDate;
    public $formattedEndDate;
    public $recipientType;

    public function __construct(InstrumentRental $rental, string $recipientType = 'admin')
    {
        $this->rental = $rental;
        $this->recipientType = $recipientType;
        
        // Format the dates for better readability
        $this->formattedStartDate = Carbon::parse($rental->rental_start_date)->format('l, F j, Y');
        $this->formattedEndDate = Carbon::parse($rental->rental_end_date)->format('l, F j, Y');
    }

    public function build()
    {
        $subject = $this->recipientType === 'user'
            ? 'Instrument Rental Confirmation - Lemon Hub Studio'
            : 'Instrument Rental Request - Lemon Hub Studio';
        
        return $this->subject($subject)
                    ->view('emails.instrument-rental-notification')
                    ->with([
                        'rentalReference' => $this->rental->reference,
                        'paymentReference' => $this->rental->payment_reference,
                        'instrumentType' => $this->rental->instrument_type,
                        'instrumentName' => $this->rental->instrument_name,
                        'startDate' => $this->formattedStartDate,
                        'endDate' => $this->formattedEndDate,
                        'duration' => $this->rental->rental_duration_days . ' day' . ($this->rental->rental_duration_days > 1 ? 's' : ''),
                        'downPayment' => number_format($this->rental->reservation_fee ?? 0, 2),
                        'totalAmount' => number_format($this->rental->total_amount, 2),
                        'pickupLocation' => $this->rental->pickup_location,
                        'notes' => $this->rental->notes ?? 'None',
                        'status' => ucfirst($this->rental->status),
                        'studioName' => 'Lemon Hub Studio',
                        'studioEmail' => 'magamponr@gmail.com',
                        'customerName' => $this->rental->user->name ?? 'N/A',
                        'customerEmail' => $this->rental->user->email ?? ($this->rental->email ?? 'N/A'),
                        'recipientType' => $this->recipientType,
                    ]);
    }
}