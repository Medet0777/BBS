<?php

namespace App\Contracts\Services\Http\Api\V1;

use App\Http\Requests\Api\V1\Barbershop\ListRequest;
use App\Http\Requests\Api\V1\Barbershop\SlotsRequest;
use Illuminate\Http\JsonResponse;

interface BarbershopServiceContract
{

    /**
     * @param ListRequest $request
     *
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse;

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse;

    /**
     * @param string       $slug
     * @param SlotsRequest $request
     *
     * @return JsonResponse
     */
    public function availableSlots(string $slug, SlotsRequest $request): JsonResponse;
}
