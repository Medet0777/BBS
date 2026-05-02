<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @property string $password
 *
 * @method static where(string $string, string $email)
 * @method static create(array $data)
 */
class User extends Authenticatable
{

    use HasApiTokens, CrudTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'email_verified_at',
        'reset_otp_code',
        'reset_otp_expires_at',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
