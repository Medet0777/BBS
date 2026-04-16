<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{

    use Queueable, SerializesModels;

    /**
     * @param Booking $booking
     */
    public function __construct(
        public readonly Booking $booking,
    ) {
    }

    /**
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Your booking is in 2 hours',
        );
    }

    /**
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.booking-reminder',
        );
    }
}
