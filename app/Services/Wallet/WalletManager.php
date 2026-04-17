<?php

namespace App\Services\Wallet;

use Illuminate\Support\Facades\DB;

class WalletManager
{
    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function safeCredit($user, float $amount)
{
    return DB::transaction(function () use ($user, $amount) {

        if (!$user->wallet) {
            $this->walletService->createWallet($user);
            $user->refresh();
        }

        $wallet = $user->wallet()->lockForUpdate()->first();

        $this->walletService->credit($user, $amount);

        return $wallet->fresh();
    });
}
    public function safeDebit($user, float $amount)
{
    return DB::transaction(function () use ($user, $amount) {

        if (!$user->wallet) {
            throw new Exception('Wallet not found');
        }

        $wallet = $user->wallet()->lockForUpdate()->first();

        $this->walletService->debit($user, $amount);

        return $wallet->fresh();
    });
}
}