<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;

class SmsService
{
    public function send($phone, $otp)
    {
        $response = Http::asForm()->post('https://smsmisr.com/api/OTP/', [
            'environment' => 1,
            'username' => env('SMSMISR_USERNAME'),
            'password' => env('SMSMISR_PASSWORD'),
            'sender' => env('SMSMISR_SENDER'),
            'mobile' => $phone,
            'template' => '0f9217c9d760c1c0ed47b8afb5425708da7d98729016a8accfc14f9cc8d1ba83',
            'otp' => $otp,
        ]);

        return $response->json();
    }
}