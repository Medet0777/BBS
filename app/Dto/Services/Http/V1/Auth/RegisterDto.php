<?php

namespace App\Dto\Services\Http\V1\Auth;

use Spatie\LaravelData\Data;

class RegisterDto extends Data
{
    /**
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}
}
