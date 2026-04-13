<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'bail|required|email',
            'reset_token' => 'bail|required|string',
            'password' => 'bail|required|string|min:6|max:100|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'password.required' => 'New password is required',
            'password.string' => 'Password must be valid',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password is too long',
            'password.confirmed' => 'Passwords do not match',
            'reset_token.required' => 'Reset token is required',
            'reset_token.string' => 'Reset token must be valid',
        ];
    }

    public function attributes()
    {
        return [
            'password' => 'Password',
            'password_confirmation' => 'Confirm Password',
            
        ];
    }
}