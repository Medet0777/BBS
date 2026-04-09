<?php

namespace App\Contracts\Services\Http\Api\V1;

use Illuminate\Http\JsonResponse;

interface BarbershopServiceContract
{

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse;

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse;
}
