<?php

namespace App\Contracts\Services\Http\Api\V1;

use App\Http\Requests\Api\V1\Owner\CalendarRequest;
use Illuminate\Http\JsonResponse;

interface OwnerServiceContract
{

    /**
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse;

    /**
     * @param CalendarRequest $request
     *
     * @return JsonResponse
     */
    public function calendar(CalendarRequest $request): JsonResponse;
}
