<?php

namespace App\Services\Notification;

use App\Services\BrevoMailService;
use App\Services\Notification\SmsService;

class NotificationService
{
    protected $brevo;
    protected $sms;

    public function __construct(BrevoMailService $brevo, SmsService $sms)
    {
        $this->brevo = $brevo;
        $this->sms = $sms;
    }

public function sendOtp($user, $otp, $type = 'verification')
{
    $smsResponse = null;

    if (!empty($user['email'])) {
        $this->brevo->sendOtp($user['email'], $otp);
    }

    if (!empty($user['phone'])) {
        $smsResponse = $this->sms->send($user['phone'], $otp);
    }

    return $smsResponse;
}
}