<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
       'identifier',
        'code',
        'type',
        'data',
        'attempts',
        'expires_at'
    ];
}
