<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'nullable|email|exists:users,email',
            'phone' => 'nullable|string|exists:users,phone',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->email && !$this->phone) {
                $validator->errors()->add('identifier', 'Email or phone is required');
            }

        });
    }

    public function messages()
    {
        return [
            'email.email' => 'Invalid email format',
            'email.exists' => 'This email is not registered',

            'phone.exists' => 'This phone is not registered',
        ];
    }
}