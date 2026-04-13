<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResendOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'bail|required|email|exists:users,email',
            'type' => 'bail|required|in:register,reset',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.exists' => 'This email is not registered',
            'type.required' => 'OTP type is required',
            'type.in' => 'OTP type must be either register or reset',
        ];
    }
}