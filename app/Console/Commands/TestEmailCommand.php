<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Booking;
use App\Mail\BookingNotification;

class TestEmailCommand extends Command
{
    protected $signature = 'test:email {email?}';
    protected $description = 'Test email notification functionality';

    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        
        $this->info('Testing email configuration...');
        
        // Test basic email sending
        try {
            Mail::raw('This is a test email from Lemon Hub Studio booking system.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Lemon Hub Studio');
            });
            
            $this->info('✓ Basic email test sent successfully to: ' . $email);
        } catch (\Exception $e) {
            $this->error('✗ Basic email test failed: ' . $e->getMessage());
            return 1;
        }
        
        // Test booking notification email if we have users and bookings
        $user = User::first();
        $booking = Booking::first();
        
        if ($user && $booking) {
            try {
                Mail::to($email)->send(new BookingNotification($booking, $user));
                $this->info('✓ Booking notification email test sent successfully');
            } catch (\Exception $e) {
                $this->error('✗ Booking notification email test failed: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->warn('⚠ No users or bookings found to test booking notification email');
        }
        
        $this->info('Email testing completed!');
        return 0;
    }
}