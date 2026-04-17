<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:10|max:100000',
            'method' => 'required|in:card,wallet'
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Amount is required',
            'amount.min' => 'Minimum is 10 EGP',
            'method.required' => 'Payment method required',
            'method.in' => 'Invalid payment method'
        ];
    }
}