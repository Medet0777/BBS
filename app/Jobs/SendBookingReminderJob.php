<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingReminderJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param int $bookingId
     */
    public function __construct(
        private readonly int $bookingId,
    ) {
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $booking = Booking::with(['user', 'barbershop', 'barber'])->find($this->bookingId);

        if (!$booking) {
            return;
        }

        if (in_array($booking->status, [BookingStatus::Cancelled, BookingStatus::Completed], true)) {
            return;
        }

        Mail::to($booking->user->email)->send(new BookingReminderMail($booking));
    }
}
