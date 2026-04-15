<?php

namespace App\Contracts\Services\Http\Api\V1;

use Illuminate\Http\JsonResponse;

interface OwnerServiceContract
{

    /**
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse;
}
