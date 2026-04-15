<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use App\Services\Wallet\WalletManager;
use App\Http\Resources\Wallet\WalletResource;
use App\Http\Requests\Wallet\AddBalanceRequest; // ✅ مهم

class WalletController extends Controller
{
    protected $walletService;
    protected $walletManager;

    public function __construct(
        WalletService $walletService,
        WalletManager $walletManager
    ) {
        $this->walletService = $walletService;
        $this->walletManager = $walletManager;
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
}