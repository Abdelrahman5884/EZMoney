<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class AddBalanceRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:1'
        ];
    }
}