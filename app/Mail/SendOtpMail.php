<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $type;

    public function __construct($otp, $type = 'verification')
    {
        $this->otp = $otp;
        $this->type = $type;
    }

    public function build()
    {
        return $this->subject(
                $this->type === 'reset'
                    ? 'Reset Your Password - EZMoney'
                    : 'Verify Your Account - EZMoney'
            )
            ->markdown('emails.otp')
            ->with([
                'otp' => $this->otp,
                'type' => $this->type,
            ]);
    }
}