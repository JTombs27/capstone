<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('SEMAPHORE_API_KEY');
    }

    public function sendSMS($number, $message)
    {
        $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => $this->apiKey,
            'number' => $number,
            'message' => $message,
            'sendername' => '', // Change this to your registered Sender Name in Semaphore
        ]);
        return $response->json();
    }

    public function getSmsStatus($messageId)
    {
        $response = Http::get('https://api.semaphore.co/api/v4/messages', [
            'apikey' => $this->apiKey,
            'message_id' => $messageId,
        ])->json();

        return $response;
    }
}
