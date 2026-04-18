<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\Wallet\WalletController;
use App\Http\Controllers\Security\PinController;

// ================= AUTH =================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/set-pin', [PinController::class, 'setPin']);
    Route::post('/verify-pin', [PinController::class, 'verifyPin']);

});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/wallet', [WalletController::class, 'index']);
    Route::post('/wallet/add', [WalletController::class, 'addBalance']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
});

use App\Http\Controllers\Wallet\PaymentController;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/wallet/deposit', [PaymentController::class, 'deposit']);
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);

});

Route::match(['GET', 'POST'], '/payment/webhook', [PaymentController::class, 'webhook']);
Route::post('/payment/payout-webhook', [PaymentController::class, 'payoutWebhook']);