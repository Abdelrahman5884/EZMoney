<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'bail|required|email',
            'otp' => 'bail|required|digits:6',
            'type' => 'bail|required|in:register,reset',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',

            'otp.required' => 'OTP code is required',
            'otp.digits' => 'OTP must be 6 digits',
            'type.required' => 'OTP type is required',
            'type.in' => 'OTP type must be either register or reset',
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'Email',
            'otp' => 'OTP Code',
            'type' => 'OTP Type',
        ];
    }
}