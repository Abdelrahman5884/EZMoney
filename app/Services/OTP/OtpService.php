<?php

namespace App\Services\OTP;

use App\Models\Otp;
use Illuminate\Support\Facades\DB;

class OtpService
{
    protected int $expiryMinutes = 10;
    protected int $cooldownSeconds = 60;
    protected int $maxAttempts = 5;

    // ================= GENERATE OTP =================

    public function generate(string $identifier, string $type = 'verification', array $data = null): string
    {
        return DB::transaction(function () use ($identifier, $type, $data) {

            // 🔒 Prevent spam (cooldown)
            $recentOtp = Otp::where('identifier', $identifier)
                ->where('type', $type)
                ->where('created_at', '>', now()->subSeconds($this->cooldownSeconds))
                ->first();

            if ($recentOtp) {
                throw new \Exception('Please wait before requesting another OTP');
            }

            // 🧹 delete old OTPs
            Otp::where('identifier', $identifier)
                ->where('type', $type)
                ->delete();

            // 🔢 generate OTP
            $otp = (string) rand(100000, 999999);

            Otp::create([
                'identifier' => $identifier,
                'type' => $type,
                'code' => $otp,
                'data' => $data ? json_encode($data) : null,
                'attempts' => 0,
                'expires_at' => now()->addMinutes($this->expiryMinutes),
            ]);

            return $otp;
        });
    }

    // ================= VERIFY OTP =================

    public function verify(string $identifier, string $code, string $type = 'verification'): ?Otp
    {
        $otp = Otp::where('identifier', $identifier)
            ->where('type', $type)
            ->latest()
            ->first();

        // ❌ Not found
        if (!$otp) {
            return null;
        }

        // ⛔ expired
        if ($otp->expires_at < now()) {
            $otp->delete();
            return null;
        }

        // 🔒 attempts limit
        if ($otp->attempts >= $this->maxAttempts) {
            $otp->delete();
            return null;
        }

        // ❌ wrong code
        if ($otp->code !== $code) {
            $otp->increment('attempts');
            return null;
        }

        // ✅ success → delete OTP
        $otp->delete();

        return $otp;
    }
}