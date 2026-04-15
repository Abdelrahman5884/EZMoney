<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailService
{
    public function sendOtp($to, $otp)
    {
        return Http::withHeaders([
            'api-key' => config('services.brevo.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => config('mail.from.name'),
                'email' => config('mail.from.address'),
            ],
            'to' => [
                ['email' => $to]
            ],
            'subject' => 'Your EZMoney Verification Code',
            'htmlContent' => "
<!DOCTYPE html>
<html>
<body style='margin:0;padding:0;background:#0f172a;font-family:Arial;'>

<table width='100%' cellpadding='0' cellspacing='0'>
<tr>
<td align='center'>

<table width='520' style='background:#ffffff;margin:40px auto;border-radius:16px;overflow:hidden;'>

<!-- Header -->
<tr>
<td style='background:#1e3a8a;padding:25px;text-align:center;color:#fff;'>
<h1 style='margin:0;'>EZMoney</h1>
<p style='margin:5px 0 0;font-size:13px;'>Secure Wallet</p>
</td>
</tr>

<!-- Body -->
<tr>
<td style='padding:40px;text-align:center;'>

<h2 style='color:#111;'>Verification Code</h2>

<p style='color:#555;font-size:15px;margin-top:10px;'>
Use the code below to continue your request
</p>

<div style='margin:30px 0;'>

<span style='display:inline-block;
background:#eff6ff;
padding:18px 40px;
font-size:32px;
font-weight:bold;
letter-spacing:10px;
border-radius:12px;
color:#1e3a8a;'>
{$otp}
</span>

</div>

<p style='color:#888;font-size:13px;'>
This code expires in <b>10 minutes</b>
</p>

<hr style='margin:30px 0;border:none;border-top:1px solid #eee;'>

<p style='font-size:12px;color:#999;'>
If you didn’t request this, ignore this email.
</p>

</td>
</tr>

<!-- Footer -->
<tr>
<td style='background:#f1f5f9;padding:20px;text-align:center;font-size:12px;color:#666;'>
© " . date('Y') . " EZMoney. All rights reserved.
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
"
        ]);
    }
}