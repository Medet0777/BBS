<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailService
{

    private const ENDPOINT = 'https://api.brevo.com/v3/smtp/email';

    /**
     * @param string $toEmail
     * @param string $toName
     * @param string $subject
     * @param string $htmlContent
     *
     * @return bool
     */
    public function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $apiKey    = config('services.brevo.api_key');
        $fromEmail = config('services.brevo.from_email');
        $fromName  = config('services.brevo.from_name', 'BBS');

        if (!$apiKey) {
            error_log('[BREVO] API key is not configured');

            return false;
        }

        if (!$fromEmail) {
            error_log('[BREVO] FROM email is not configured');

            return false;
        }

        try {
            $response = Http::timeout(15)->withHeaders([
                'api-key'      => $apiKey,
                'accept'       => 'application/json',
                'content-type' => 'application/json',
            ])->post(self::ENDPOINT, [
                'sender' => ['name' => $fromName, 'email' => $fromEmail],
                'to'     => [['email' => $toEmail, 'name' => $toName]],
                'subject'     => $subject,
                'htmlContent' => $htmlContent,
            ]);
        } catch (\Throwable $e) {
            error_log('[BREVO] HTTP exception: ' . $e->getMessage());

            return false;
        }

        if (!$response->successful()) {
            error_log('[BREVO] Email failed status=' . $response->status() . ' body=' . $response->body());

            return false;
        }

        error_log('[BREVO] Email sent OK to ' . $toEmail);

        return true;
    }
}
