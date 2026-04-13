<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class NotificationService
{
    public function sendOtp($user, $otp, $type = 'verification')
    {
        // Email
        if (!empty($user['email'])) {
            Mail::to($user['email'])->send(new SendOtpMail($otp, $type));
        }

        // Phone (SMS) - Placeholder
        if (!empty($user['phone'])) {
            $this->sendSms($user['phone'], $otp);
        }
    }

    private function sendSms($phone, $otp)
    {
        // TODO: integrate with SMS provider (Twilio / Vodafone / etc)
        // مثال:
        // Http::post('sms-api', [...]);

        // مؤقتًا:
        logger("OTP for $phone is $otp");
    }
}