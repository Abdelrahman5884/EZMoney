<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    protected $message;

    public function __construct($resource, $message = 'Success')
    {
        parent::__construct($resource);
        $this->message = $message;
    }

    public function toArray($request)
    {
        return [
            'status' => true,
            'message' => $this->message,

            'data' => [
                'user' => [
                    'id' => $this->id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'created_at' => $this->created_at?->toDateTimeString(),
                ],

                'auth' => [
                    'token' => $this->when(isset($this->token), $this->token),
                    'token_type' => $this->when(isset($this->token), 'Bearer'),
                ],
            ]
        ];
    }
}