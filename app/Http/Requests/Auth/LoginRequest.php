<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'login' => 'bail|required|string',
            'password' => 'bail|required|string|min:6',
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Email or phone is required',
            'login.string' => 'Login must be valid',

            'password.required' => 'Password is required',
            'password.string' => 'Password must be valid',
            'password.min' => 'Password must be at least 6 characters',
        ];
    }

    public function attributes()
    {
        return [
            'login' => 'Email or Phone',
            'password' => 'Password',
        ];
    }
    public function prepareForValidation()
    {
    $this->merge([
        'login' => trim($this->login)
    ]);
    }
}