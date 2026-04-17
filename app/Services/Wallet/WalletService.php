<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
   public function getWallet($user)
{
    if (!$user->wallet) {
        return $this->createWallet($user);
    }

    return $user->wallet;
}

public function createWallet($user)
{
    return Wallet::create([
        'user_id' => $user->id,
        'balance' => 0,
    ]);
}

    public function getBalance($user): float
    {
        return (float) $user->wallet->balance;
    }

   public function credit($user, float $amount): void
{
    if ($amount <= 0) {
        throw new Exception('Invalid amount');
    }
    if (!$user->wallet) {
        $this->createWallet($user);
        $user->refresh();
    }

    $user->wallet->increment('balance', $amount);

    $this->touch($user);
}

    public function debit($user, float $amount): void
{
    if ($amount <= 0) {
        throw new Exception('Invalid amount');
    }

    if (!$user->wallet) {
        throw new Exception('Wallet not found');
    }

    if ($user->wallet->balance < $amount) {
        throw new Exception('Insufficient balance');
    }

    $user->wallet->decrement('balance', $amount);

    $this->touch($user);
}
    private function touch($user): void
    {
        $user->wallet->update([
            'last_transaction_at' => now()
        ]);
    }
}