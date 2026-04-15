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

            $wallet = $user->wallet()->lockForUpdate()->first();

            $this->walletService->credit($user, $amount);

            return $wallet->fresh();
        });
    }

    public function safeDebit($user, float $amount)
    {
        return DB::transaction(function () use ($user, $amount) {

            $wallet = $user->wallet()->lockForUpdate()->first();

            $this->walletService->debit($user, $amount);

            return $wallet->fresh();
        });
    }
}