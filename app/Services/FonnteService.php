<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Send a WhatsApp message using Fonnte API.
     *
     * @param string|array $target Phone number(s) (comma-separated or array)
     * @param string $message The message body
     * @return bool True if successful, false otherwise
     */
    public static function send($target, string $message): bool
    {
        $token = config('services.fonnte.token') ?? env('FONNTE_TOKEN');
        if (empty($token)) {
            Log::warning('Fonnte token is not configured.');
            return false;
        }

        if (is_array($target)) {
            $target = implode(',', $target);
        }

        // Clean target phone numbers to standard format (replace leading '0' with '62')
        $target = self::formatPhoneNumber($target);

        if (empty($target)) {
            Log::warning('Fonnte message not sent because target phone number list is empty after formatting.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'delay' => '2',
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message successfully sent to {$target} via Fonnte.");
                return true;
            }

            Log::error("Fonnte API returned error: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message via Fonnte: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format Indonesian phone numbers to start with 62 instead of 0 or +62.
     */
    private static function formatPhoneNumber(string $target): string
    {
        $numbers = explode(',', $target);
        $formatted = [];

        foreach ($numbers as $num) {
            $num = trim($num);
            if (empty($num)) continue;

            // Remove any characters that are not digits
            $num = preg_replace('/\D/', '', $num);

            // If it starts with '0', replace with '62'
            if (str_starts_with($num, '0')) {
                $num = '62' . substr($num, 1);
            }

            if (!empty($num)) {
                $formatted[] = $num;
            }
        }

        return implode(',', $formatted);
    }
}
