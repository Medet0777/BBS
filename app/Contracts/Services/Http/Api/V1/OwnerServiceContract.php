<?php

namespace App\Contracts\Services\Http\Api\V1;

use App\Http\Requests\Api\V1\Owner\AnalyticsRequest;
use App\Http\Requests\Api\V1\Owner\BookingListRequest;
use App\Http\Requests\Api\V1\Owner\CalendarRequest;
use App\Http\Requests\Api\V1\Owner\ServiceStoreRequest;
use App\Http\Requests\Api\V1\Owner\ServiceUpdateRequest;
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

    /**
     * @param BookingListRequest $request
     *
     * @return JsonResponse
     */
    public function bookings(BookingListRequest $request): JsonResponse;

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function cancelBooking(int $id): JsonResponse;

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function completeBooking(int $id): JsonResponse;

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function confirmBooking(int $id): JsonResponse;

    /**
     * @return JsonResponse
     */
    public function listServices(): JsonResponse;

    /**
     * @param ServiceStoreRequest $request
     *
     * @return JsonResponse
     */
    public function createService(ServiceStoreRequest $request): JsonResponse;

    /**
     * @param int                  $id
     * @param ServiceUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function updateService(int $id, ServiceUpdateRequest $request): JsonResponse;

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteService(int $id): JsonResponse;

    /**
     * @param AnalyticsRequest $request
     *
     * @return JsonResponse
     */
    public function analytics(AnalyticsRequest $request): JsonResponse;
}
