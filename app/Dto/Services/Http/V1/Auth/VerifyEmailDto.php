<?php

namespace App\Dto\Services\Http\V1\Auth;

use Spatie\LaravelData\Data;

class VerifyEmailDto extends Data
{

    /**
     * @param string $email
     * @param string $code
     */
    public function __construct(
        public readonly string $email,
        public readonly string $code,
    ) {}
}
