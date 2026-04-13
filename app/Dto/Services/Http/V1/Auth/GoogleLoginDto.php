<?php

namespace App\Dto\Services\Http\V1\Auth;

use Spatie\LaravelData\Data;

class GoogleLoginDto extends Data
{
    /**
     * @param string $id_token
     */
    public function __construct(
        public readonly string $id_token,
    ) {}
}
