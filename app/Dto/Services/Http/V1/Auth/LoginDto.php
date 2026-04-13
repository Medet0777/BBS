<?php

namespace App\Dto\Services\Http\V1\Auth;

use Spatie\LaravelData\Data;

class LoginDto extends Data
{
    /**
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}
}
