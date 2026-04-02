<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{

    use HasApiTokens;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',

    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
