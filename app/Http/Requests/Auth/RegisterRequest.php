<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'bail|required|string|min:3|max:255',
            'email' => 'bail|required|email|max:255|unique:users,email',
            'phone' => 'bail|required|string|min:10|max:15|unique:users,phone',
            'password' => 'bail|required|string|min:6|max:100',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a valid text',
            'name.min' => 'Name must be at least 3 characters',
            'name.max' => 'Name is too long',

            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email is too long',
            'email.unique' => 'This email is already registered',

            'phone.required' => 'Phone number is required',
            'phone.string' => 'Phone must be valid',
            'phone.min' => 'Phone number is too short',
            'phone.max' => 'Phone number is too long',
            'phone.unique' => 'This phone number is already registered',

            'password.required' => 'Password is required',
            'password.string' => 'Password must be valid',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password is too long',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone number',
            'password' => 'Password',
        ];
    }
}
