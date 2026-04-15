<?php

namespace App\Contracts\Services\Http\Api\V1;

use App\Http\Requests\Api\V1\Review\CreateRequest;
use Illuminate\Http\JsonResponse;

interface ReviewServiceContract
{

    /**
     * @param string        $slug
     * @param CreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(string $slug, CreateRequest $request): JsonResponse;
}
