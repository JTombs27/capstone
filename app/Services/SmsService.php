<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

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
            'sendername' => 'DDOPVOADS', // Change this to your registered Sender Name in Semaphore
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

    public function getBalance()
    {
        try {
            $response = Http::timeout(30)->get('https://api.semaphore.co/api/v4/account', [
                'apikey' => $this->apiKey,
            ]);
    
            return $response->json();
        } catch (Exception $e) {
            Log::error('Error fetching balance: ' . $e->getMessage());
            return ['error' => 'Failed to retrieve balance'];
        }
    }
}
