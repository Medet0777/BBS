<?php

namespace App\Contracts\Services\Http\Api\V1;

use App\Http\Requests\Api\V1\Booking\CreateRequest;
use App\Http\Requests\Api\V1\Booking\ListRequest;
use App\Http\Requests\Api\V1\Booking\RescheduleRequest;
use Illuminate\Http\JsonResponse;

interface BookingServiceContract
{

    /**
     * @param CreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(CreateRequest $request): JsonResponse;

    /**
     * @param ListRequest $request
     *
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse;

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse;

    /**
     * @param int               $id
     * @param RescheduleRequest $request
     *
     * @return JsonResponse
     */
    public function reschedule(int $id, RescheduleRequest $request): JsonResponse;
}
