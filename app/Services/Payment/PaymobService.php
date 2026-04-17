<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;

class PaymobService
{
    protected $baseUrl = "https://accept.paymob.com/api";

    public function authenticate()
    {
        return Http::post($this->baseUrl.'/auth/tokens', [
            'api_key' => config('services.paymob.api_key')
        ])->json()['token'];
    }

    public function createOrder($token, $amount)
    {
        return Http::post($this->baseUrl.'/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => false,
            'amount_cents' => $amount * 100,
            'currency' => 'EGP',
            'items' => []
        ])->json();
    }

   public function paymentKey($token, $orderId, $amount, $user, $method = 'card')
{
    $integrationId = $method === 'wallet'
        ? config('services.paymob.wallet_integration_id')
        : config('services.paymob.card_integration_id');

    return Http::post($this->baseUrl.'/acceptance/payment_keys', [
        'auth_token' => $token,
        'amount_cents' => $amount * 100,
        'expiration' => 3600,
        'order_id' => $orderId,

        'billing_data' => [
            "first_name" => $user->name,
            "last_name" => "User",
            "email" => $user->email ?? "test@test.com",
            "phone_number" => $user->phone,
            "country" => "EG",
            "city" => "Cairo",
            "street" => "NA",
            "building" => "NA",
            "floor" => "NA",
            "apartment" => "NA"
        ],

        'currency' => 'EGP',
        'integration_id' => $integrationId,

        'extra' => [
            'user_id' => $user->id
        ]

    ])->json()['token'];
}

    public function iframe($paymentToken)
    {
        return "https://accept.paymob.com/api/acceptance/iframes/"
            . config('services.paymob.iframe_id')
            . "?payment_token=" . $paymentToken;
    }
    public function walletPayment($paymentToken, $phone)
{
    return Http::post($this->baseUrl . '/acceptance/payments/pay', [
        "source" => [
            "identifier" => $phone,
            "subtype" => "WALLET"
        ],
        "payment_token" => $paymentToken
    ])->json();
}
public function walletPayout($amount, $phone)
{
    $token = $this->authenticate();

    $response = Http::post($this->baseUrl . '/disbursements/payouts', [
        "auth_token" => $token,
        "amount" => $amount,
        "beneficiary_name" => "User",
        "beneficiary_mobile" => $phone,
        "description" => "Withdraw from EZMoney",
        "callback_url" => config('app.url') . '/api/payment/payout-webhook'
    ])->json();

    return $response;
}
}