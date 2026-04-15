<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
     protected $fillable = [
        'user_id',
        'balance',
        'last_transaction_at'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_transaction_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
