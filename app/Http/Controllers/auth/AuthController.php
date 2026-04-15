<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{
    RegisterRequest,
    LoginRequest,
    VerifyOtpRequest,
    ForgotPasswordRequest,
    ResetPasswordRequest,
    ResendOtpRequest
};
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // ================= REGISTER =================

    public function register(RegisterRequest $request)
    {
        $response = $this->authService->register($request->validated());

        if (!$response['status']) {
            return response()->json($response, 400);
        }

        return response()->json($response, 200);
    }

    // ================= VERIFY OTP =================

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $type = $request->input('type', 'register');

        $user = $this->authService->verifyOtp($request->validated(), $type);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        return new AuthResource($user, 'OTP verified successfully');
    }

    // ================= RESEND OTP =================

    public function resendOtp(ResendOtpRequest $request)
    {
        $type = $request->input('type', 'register');

        $response = $this->authService->resendOtp($request->validated(), $type);

        if (!$response['status']) {
            return response()->json($response, 400);
        }

        return response()->json($response, 200);
    }

    // ================= LOGIN =================

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request->validated());

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email/phone or password'
            ], 401);
        }

        return new AuthResource($user, 'Login successful');
    }

    // ================= FORGOT PASSWORD =================

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $response = $this->authService->forgotPassword($request->validated());

        if (!$response['status']) {
            return response()->json($response, 400);
        }

        return response()->json($response, 200);
    }

    // ================= RESET PASSWORD =================

    public function resetPassword(ResetPasswordRequest $request)
    {
        $response = $this->authService->resetPassword($request->validated());

        if (!$response) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired reset token'
            ], 400);
        }

        return response()->json($response, 200);
    }

    // ================= LOGOUT =================

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}