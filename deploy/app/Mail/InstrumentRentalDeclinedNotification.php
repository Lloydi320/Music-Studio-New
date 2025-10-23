<?php

namespace App\Mail;

use App\Models\InstrumentRental;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class InstrumentRentalDeclinedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $rental;
    public $formattedStartDate;
    public $formattedEndDate;

    public function __construct(InstrumentRental $rental)
    {
        $this->rental = $rental;
        $this->formattedStartDate = Carbon::parse($rental->rental_start_date)->format('l, F j, Y');
        $this->formattedEndDate = Carbon::parse($rental->rental_end_date)->format('l, F j, Y');
    }

    public function build()
    {
        return $this->subject('Instrument Rental Declined - Lemon Hub Studio')
                    ->view('emails.instrument-rental-declined')
                    ->with([
                        'rentalReference' => $this->rental->reference,
                        'paymentReference' => $this->rental->payment_reference,
                        'instrumentType' => $this->rental->instrument_type,
                        'instrumentName' => $this->rental->instrument_name,
                        'startDate' => $this->formattedStartDate,
                        'endDate' => $this->formattedEndDate,
                        'duration' => $this->rental->rental_duration_days . ' day' . ($this->rental->rental_duration_days > 1 ? 's' : ''),
                        'studioName' => 'Lemon Hub Studio',
                        'studioEmail' => 'magamponr@gmail.com',
                        'customerName' => optional($this->rental->user)->name ?? ($this->rental->name ?? 'Customer'),
                        'customerEmail' => optional($this->rental->user)->email ?? ($this->rental->email ?? 'N/A'),
                    ]);
    }
}