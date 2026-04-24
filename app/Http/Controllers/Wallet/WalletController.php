<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use App\Services\Wallet\WalletManager;
use App\Http\Resources\Wallet\WalletResource;
use App\Http\Requests\Wallet\AddBalanceRequest; 
use App\Services\Wallet\TransferService;
use App\Http\Requests\Wallet\TransferRequest;
use App\Http\Requests\Wallet\WithdrawRequest;
use App\Models\Transaction;
use App\Models\Wallet;

class WalletController extends Controller
{
    protected $walletService;
    protected $walletManager;
    protected $transferService;

    public function __construct(
        WalletService $walletService,
        WalletManager $walletManager,
        TransferService $transferService
        
    ) {
        $this->walletService = $walletService;
        $this->walletManager = $walletManager;
        $this->transferService = $transferService;
    }

    public function index()
    {
        $wallet = $this->walletService->getWallet(auth()->user());
        return new WalletResource($wallet);
    }

    public function addBalance(AddBalanceRequest $request)
    {
        $wallet = $this->walletManager->safeCredit(
            $request->user(),
            $request->amount
        );

        return new WalletResource($wallet);
    }
      public function transfer(TransferRequest $request)
    {
    $transaction = $this->transferService->transfer(
        $request->user(),
        $request->to_user_id,
        $request->amount
    );

    return response()->json([
        'status' => true,
        'message' => 'Transfer successful',
        'data' => $transaction
    ]);
   }
public function withdraw(WithdrawRequest $request)
{
    $user = $request->user();
    $amount = $request->amount;
    $phone = $request->phone;

    $wallet = Wallet::where('user_id', $user->id)->first();

    if (!$wallet || $wallet->balance < $amount) {
        return response()->json([
            'status' => false,
            'message' => 'Insufficient balance'
        ], 400);
    }

    $wallet->decrement('balance', $amount);

    $transaction = Transaction::create([
        'from_user_id' => $user->id,
        'to_user_id' => null,
        'amount' => $amount,
        'type' => 'withdraw',
        'status' => 'pending',
        'provider' => 'manual',
        'meta' => json_encode([
            'phone' => $phone
        ])
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Withdraw request submitted',
        'transaction_id' => $transaction->id
    ]);
}
}