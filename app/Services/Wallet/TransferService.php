<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService
{
    protected $walletManager;

    public function __construct(WalletManager $walletManager)
    {
        $this->walletManager = $walletManager;
    }

    public function transfer($fromUser, $toUserId, float $amount)
    {
        if ($amount <= 0) {
            throw new Exception('Invalid amount');
        }

        $toUser = User::find($toUserId);

        if (!$toUser) {
            throw new Exception('Receiver not found');
        }

        if ($fromUser->id == $toUser->id) {
            throw new Exception('Cannot transfer to yourself');
        }

        return DB::transaction(function () use ($fromUser, $toUser, $amount) {

            // 💸 خصم
            $this->walletManager->safeDebit($fromUser, $amount);

            // 💰 إضافة
            $this->walletManager->safeCredit($toUser, $amount);

            // 🧾 تسجيل العملية
            $transaction = Transaction::create([
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'amount' => $amount,
                'type' => 'transfer',
                'status' => 'completed'
            ]);

            return $transaction;
        });
    }
}