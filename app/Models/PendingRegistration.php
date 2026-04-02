<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'otp_code',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'otp_expires_at' => 'datetime',
        ];
    }
}