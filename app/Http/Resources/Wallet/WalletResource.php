<?php

namespace App\Http\Resources\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'balance' => (float) $this->balance,
            'last_transaction_at' => $this->last_transaction_at
        ];
    }
}