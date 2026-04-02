<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{

    /**
     * @var string
     */
    private string $token;

    /**
     * @param        $resource
     *
     * @param string $token
     */
    public function __construct($resource, string $token)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'user'  => UserResource::make($this->resource),
            'token' => $this->token,
        ];
    }
}
