<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Booking Reminder</h2>
    <p>Hi {{ $booking->user?->name }},</p>
    <p>Your appointment is coming up in <strong>2 hours</strong>!</p>

    <table style="border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 8px; font-weight: bold;">Barbershop:</td>
            <td style="padding: 8px;">{{ $booking->barbershop?->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Address:</td>
            <td style="padding: 8px;">{{ $booking->barbershop?->address }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Barber:</td>
            <td style="padding: 8px;">{{ $booking->barber?->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Date & Time:</td>
            <td style="padding: 8px;">{{ $booking->scheduled_at->format('d.m.Y H:i') }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Total:</td>
            <td style="padding: 8px;">{{ $booking->total_price }} ₸</td>
        </tr>
    </table>

    <p>See you soon!</p>
    <p>— BBS Team</p>
</body>
</html>
