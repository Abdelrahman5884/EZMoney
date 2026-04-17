<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class TransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'to_user_id' => 'bail|required|integer|exists:users,id',
            'amount' => 'bail|required|numeric|min:1|max:100000'
        ];
    }

    public function messages()
    {
        return [
            
            'to_user_id.required' => 'Receiver is required',
            'to_user_id.integer' => 'Receiver ID must be a number',
            'to_user_id.exists' => 'Receiver not found',

            
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a valid number',
            'amount.min' => 'Minimum transfer amount is 1',
            'amount.max' => 'Amount exceeds allowed limit',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $user = auth()->user();

            if ($this->to_user_id == $user->id) {
                $validator->errors()->add('to_user_id', 'You cannot transfer to yourself');
            }

            $receiver = User::find($this->to_user_id);
            if (!$receiver) {
                $validator->errors()->add('to_user_id', 'Receiver not found');
            }


            if ($user->wallet && $this->amount > $user->wallet->balance) {
                $validator->errors()->add('amount', 'Insufficient balance');
            }

        });
    }
}