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
        $apiKey = config('services.brevo.api_key');

        if (!$apiKey) {
            Log::warning('Brevo API key is not configured');

            return false;
        }

        $response = Http::withHeaders([
            'api-key'      => $apiKey,
            'accept'       => 'application/json',
            'content-type' => 'application/json',
        ])->post(self::ENDPOINT, [
            'sender' => [
                'name'  => config('services.brevo.from_name', 'BBS'),
                'email' => config('services.brevo.from_email'),
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName],
            ],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ]);

        if (!$response->successful()) {
            Log::error('Brevo email failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
