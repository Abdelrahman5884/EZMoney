<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendOtpMail extends Mailable
{
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
                    ? 'Reset Your Password - EZMonay'
                    : 'Verify Your Account - EZMonay'
            )
            ->markdown('emails.otp');
    }
}