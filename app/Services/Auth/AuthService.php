<?php 

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\OTP\OtpService;
use App\Services\Notification\NotificationService;
use Laravel\Sanctum\PersonalAccessToken;


class AuthService
{
    private $otpService;
    private $notificationService;

    public function __construct(
        OtpService $otpService,
        NotificationService $notificationService
    ) {
        $this->otpService = $otpService;
        $this->notificationService = $notificationService;
    }

    // ================= REGISTER =================

   public function register($data)
{
    $identifier = $data['email'] ?? $data['phone'];

    if (isset($data['email']) && User::where('email', $data['email'])->exists()) {
        return [
            'status' => false,
            'message' => 'Email already registered'
        ];
    }

    if (isset($data['phone']) && User::where('phone', $data['phone'])->exists()) {
        return [
            'status' => false,
            'message' => 'Phone already registered'
        ];
    }

    $otp = $this->otpService->generate($identifier, 'register', $data);

    $this->notificationService->sendOtp($data, $otp, 'register');

    return [
        'status' => true,
        'message' => 'OTP sent successfully'
    ];
}

    // ================= VERIFY OTP =================
public function verifyOtp($data, $type = 'register')
{
    $identifier = $data['email'] ?? $data['phone'];

    $otp = $this->otpService->verify($identifier, $data['otp'], $type);

    if (!$otp) return null;

    // ================= REGISTER =================
    if ($type === 'register') {

        $userData = json_decode($otp->data, true);

        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
            'password' => bcrypt($userData['password'])
        ]);

        $otp->delete();

        $user->token = $user->createToken('auth_token')->plainTextToken;

        return $user;
    }

    // ================= RESET =================
    if ($type === 'reset') {

        $user = User::where('email', $identifier)->first();
        $user->token = $user->createToken('reset_token')->plainTextToken;
        if (!$user) return null;

        $otp->delete();

        $user->token = $user->createToken('auth_token')->plainTextToken;

        return $user;
    }

    return null;
}

    // ================= RESEND OTP =================

public function resendOtp($data, $type = 'register')
{
    $identifier = $data['email'] ?? $data['phone'];

    $otp = $this->otpService->generate($identifier, $type, $data);

    $this->notificationService->sendOtp($data, $otp, $type);

    return [
        'status' => true,
        'message' => 'OTP resent successfully'
    ];
}
    // ================= LOGIN =================

    public function login($data)
    {
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        if (!Auth::attempt([$field => $data['login'], 'password' => $data['password']])) {
            return null;
        }

        $user = Auth::user();
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return $user;
    }

    // ================= FORGOT PASSWORD =================

public function forgotPassword($data)
{
    $identifier = $data['email'];

    $otp = $this->otpService->generate($identifier, 'reset');

    $this->notificationService->sendOtp($data, $otp, 'reset');

    return [
        'status' => true,
        'message' => 'OTP sent for password reset'
    ];
}
    // ================= RESET PASSWORD =================


public function resetPassword($data)
{
    $token = PersonalAccessToken::findToken($data['reset_token']);

    if (!$token) {
        return null;
    }

    $user = $token->tokenable;

    if (!$user) {
        return null;
    }

    $user->update([
        'password' => Hash::make($data['password'])
    ]);

    $token->delete();

    return [
        'status' => true,
        'message' => 'Password reset successfully'
    ];
}
}