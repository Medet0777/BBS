<?php

namespace App\Dto\Services\Http\V1\Auth;

use Spatie\LaravelData\Data;

class ResendCodeDto extends Data
{

    /**
     * @param string $email
     */
    public function __construct(
        public readonly string $email,
    ) {}
}
