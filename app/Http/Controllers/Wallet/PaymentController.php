<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\DepositRequest;
use App\Services\Payment\PaymobService;
use App\Services\Payment\PaymobHmacService;
use App\Models\User;
use App\Services\Wallet\WalletManager;
use Illuminate\Http\Request;
use App\Models\Transaction;
class PaymentController extends Controller
{
    protected $paymob;

    public function __construct(PaymobService $paymob)
    {
        $this->paymob = $paymob;
    }

public function deposit(DepositRequest $request)
{
    $user = $request->user();
    $amount = $request->amount;
    $method = $request->method;

    if ($method === 'wallet' && empty($user->phone)) {
        return response()->json([
            'status' => false,
            'message' => 'Phone number is required for wallet payment'
        ], 400);
    }
$token = $this->paymob->authenticate();

$order = $this->paymob->createOrder($token, $amount);

// 💾 سجل العملية Pending 🔥
Transaction::create([
    'from_user_id' => null,
    'to_user_id' => $user->id,
    'amount' => $amount,
    'type' => 'deposit',
    'status' => 'pending',
    'provider' => 'paymob',
    'provider_id' => $order['id'] // 🔥 أهم حاجة
]);

$paymentKey = $this->paymob->paymentKey(
    $token,
    $order['id'],
    $amount,
    $user,
    $method
);

    // 💳 Card
    if ($method === 'card') {
        return response()->json([
            'status' => true,
            'type' => 'iframe',
            'url' => $this->paymob->iframe($paymentKey)
        ]);
    }

    // 📱 Wallet
    if ($method === 'wallet') {

        $walletResponse = $this->paymob->walletPayment(
            $paymentKey,
            $user->phone
        );

        if (!isset($walletResponse['redirect_url'])) {
            return response()->json([
                'status' => false,
                'message' => 'Wallet payment failed',
                'data' => $walletResponse
            ], 400);
        }
return response()->json([
    'status' => true,
    'type' => 'wallet',
    'full_response' => $walletResponse
]);
    }

    return response()->json([
        'status' => false,
        'message' => 'Invalid payment method'
    ], 400);
}

public function webhook(Request $request, PaymobHmacService $hmacService)
{
if ($request->isMethod('get') && empty($request->all())) {
    return response()->json([
        'status' => true,
        'message' => 'Empty redirect'
    ]);
}

    $data = $request->all();

    \Log::info('PAYMOB WEBHOOK', $data);

    // 🔐 فعلها بعدين
    // if (!$hmacService->verify($data)) {
    //     return response()->json([
    //         'status' => false,
    //         'message' => 'Invalid HMAC - Unauthorized'
    //     ], 403);
    // }

    if (($data['success'] ?? 'false') !== 'true') {
        return response()->json([
            'status' => false,
            'message' => 'Payment failed'
        ]);
    }

    $transaction = Transaction::where('provider_id', $data['order'])->first();

    if (!$transaction) {
        return response()->json([
            'status' => false,
            'message' => 'Transaction not found'
        ]);
    }

    if ($transaction->status === 'completed') {
        return response()->json([
            'status' => false,
            'message' => 'Transaction already processed'
        ]);
    }

    $user = User::find($transaction->to_user_id);

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found'
        ]);
    }

    $amount = $data['amount_cents'] / 100;

    app(WalletManager::class)->safeCredit($user, $amount);

    $transaction->update([
        'status' => 'completed',
        'provider_transaction_id' => $data['id']
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Wallet credited successfully'
    ]);
}
public function payoutWebhook(Request $request)
{
    $data = $request->all();

    \Log::info('PAYOUT WEBHOOK', $data);

    $transaction = Transaction::where('provider_id', $data['disbursement_id'])->first();

    if (!$transaction) {
        return response()->json(['status' => false]);
    }

    if ($data['status'] !== 'SUCCESS') {
        $transaction->update(['status' => 'failed']);
        return response()->json(['status' => false]);
    }

    $transaction->update(['status' => 'completed']);

    return response()->json(['status' => true]);
}
}