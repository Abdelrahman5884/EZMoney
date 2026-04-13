<?php

namespace App\Services\OTP;

use App\Models\Otp;
use Carbon\Carbon;

class OtpService
{
   public function generate($identifier, $type = 'verification', $data = null)
{
    $otp = rand(100000, 999999);

    Otp::updateOrCreate(
        [
            'identifier' => $identifier, 
            'type' => $type
        ],
        [
            'code' => $otp,
            'data' => $data ? json_encode($data) : null,
            'expires_at' => now()->addMinutes(15)
        ]
    );

    return $otp;
}
public function verify($identifier, $code, $type = 'verification')
{
    return Otp::where('identifier', $identifier)
        ->where('code', $code)
        ->where('type', $type)
        ->where('expires_at', '>', now())
        ->first();
}
}