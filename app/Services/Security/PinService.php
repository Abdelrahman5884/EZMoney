<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PinService
{
    public function setPin($user, $pin)
    {
        $user->update([
            'transaction_pin' => Hash::make($pin),
            'pin_attempts' => 0,
            'pin_locked_until' => null
        ]);

        return true;
    }

    public function verifyPin($user, $pin)
    {
        // 🔒 check lock
        if ($user->pin_locked_until && now()->lessThan($user->pin_locked_until)) {
            return [
                'status' => false,
                'message' => 'Account locked. Try later'
            ];
        }

        // ❌ wrong PIN
        if (!Hash::check($pin, $user->transaction_pin)) {

            $user->increment('pin_attempts');

            // lock after 3 attempts
            if ($user->pin_attempts >= 3) {
                $user->update([
                    'pin_locked_until' => now()->addMinutes(5),
                    'pin_attempts' => 0
                ]);
            }

            return [
                'status' => false,
                'message' => 'Invalid PIN'
            ];
        }

        // ✅ success
        $user->update([
            'pin_attempts' => 0
        ]);

        return [
            'status' => true,
            'message' => 'PIN verified'
        ];
    }
}