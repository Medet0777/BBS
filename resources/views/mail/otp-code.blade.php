<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0f0f0f; font-family: 'Helvetica Neue', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #0f0f0f; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="420" cellpadding="0" cellspacing="0" style="background-color: #1a1a2e; border-radius: 16px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 40px 20px;">
                            <span style="font-size: 32px;">✂️</span>
                            <h1 style="margin: 10px 0 0; color: #e63946; font-size: 24px; font-weight: 700; letter-spacing: 1px;">
                                Barber<span style="color: #ffffff;">Hub</span>
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td align="center" style="padding: 10px 40px 20px;">
                            <p style="color: #a0a0b0; font-size: 15px; margin: 0 0 24px; line-height: 1.5;">
                                Your verification code:
                            </p>
                            <table cellpadding="0" cellspacing="8" style="margin: 0 auto;">
                                <tr>
                                    @foreach(str_split($otpCode) as $digit)
                                        <td align="center" style="width: 52px; height: 60px; background-color: #16213e; border: 2px solid #e63946; border-radius: 12px;">
                                            <span style="color: #ffffff; font-size: 28px; font-weight: 700;">{{ $digit }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                            <p style="color: #6b6b80; font-size: 13px; margin: 24px 0 0; line-height: 1.5;">
                                This code expires in <strong style="color: #e63946;">5 minutes</strong>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px 40px 32px; border-top: 1px solid #2a2a3e;">
                            <p style="color: #4a4a5a; font-size: 12px; margin: 0; line-height: 1.5;">
                                If you didn't request this code, please ignore this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
