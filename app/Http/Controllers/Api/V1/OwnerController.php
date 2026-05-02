<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\OwnerServiceContract;
use App\Http\Requests\Api\V1\Owner\AnalyticsRequest;
use App\Http\Requests\Api\V1\Owner\BookingListRequest;
use App\Http\Requests\Api\V1\Owner\CalendarRequest;
use App\Http\Requests\Api\V1\Owner\ServiceStoreRequest;
use App\Http\Requests\Api\V1\Owner\ServiceUpdateRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class OwnerController extends Controller
{

    /**
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/owner/dashboard',
        operationId: 'ownerDashboard',
        description: 'Returns barbershop owner dashboard with aggregated metrics for the authenticated user`s barbershop.',
        summary: 'Owner dashboard',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dashboard data',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'barbershop', properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'rating', type: 'number', format: 'float'),
                        ], type: 'object'),
                        new OA\Property(property: 'today_bookings', properties: [
                            new OA\Property(property: 'count', type: 'integer', example: 7),
                            new OA\Property(property: 'delta_vs_yesterday', type: 'integer', example: 3),
                        ], type: 'object'),
                        new OA\Property(property: 'week_revenue', properties: [
                            new OA\Property(property: 'amount', type: 'number', format: 'float', example: 85000),
                            new OA\Property(property: 'delta_pct_vs_prev', type: 'number', format: 'float', nullable: true, example: 12.5),
                        ], type: 'object'),
                        new OA\Property(property: 'new_clients_this_week', type: 'integer', example: 4),
                        new OA\Property(property: 'revenue_last_7_days', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'date', type: 'string', format: 'date'),
                            new OA\Property(property: 'revenue', type: 'number', format: 'float'),
                        ])),
                        new OA\Property(property: 'nearest_bookings', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'client_name', type: 'string'),
                            new OA\Property(property: 'barber_name', type: 'string'),
                            new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'services_count', type: 'integer'),
                            new OA\Property(property: 'total_price', type: 'number', format: 'float'),
                            new OA\Property(property: 'status', type: 'string'),
                        ])),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'User is not a barbershop owner'),
        ]
    )]
    public function dashboard(OwnerServiceContract $service): JsonResponse
    {
        return $service->dashboard();
    }

    /**
     * @param CalendarRequest      $request
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/owner/calendar',
        operationId: 'ownerCalendar',
        description: 'Returns bookings grouped by date for a given range (max 31 days).',
        summary: 'Owner calendar',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'from', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-04-01')),
            new OA\Parameter(name: 'to', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-04-30')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Calendar data',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'from', type: 'string', format: 'date'),
                        new OA\Property(property: 'to', type: 'string', format: 'date'),
                        new OA\Property(property: 'total', type: 'integer'),
                        new OA\Property(property: 'days', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'date', type: 'string', format: 'date'),
                            new OA\Property(property: 'count', type: 'integer'),
                            new OA\Property(property: 'bookings', type: 'array', items: new OA\Items(properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'client_name', type: 'string'),
                                new OA\Property(property: 'barber_name', type: 'string'),
                                new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'total_price', type: 'number', format: 'float'),
                                new OA\Property(property: 'status', type: 'string'),
                            ])),
                        ])),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Not an owner'),
            new OA\Response(response: 422, description: 'Validation error (invalid range or > 31 days)'),
        ]
    )]
    public function calendar(CalendarRequest $request, OwnerServiceContract $service): JsonResponse
    {
        return $service->calendar($request);
    }

    /**
     * @param BookingListRequest   $request
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/owner/bookings',
        operationId: 'ownerBookings',
        description: 'Paginated list of bookings for the owner`s barbershop with optional status filter',
        summary: 'List owner bookings',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'filter', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['all', 'pending', 'confirmed', 'cancelled', 'completed'])),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Bookings list'),
            new OA\Response(response: 403, description: 'Not an owner'),
        ]
    )]
    public function bookings(BookingListRequest $request, OwnerServiceContract $service): JsonResponse
    {
        return $service->bookings($request);
    }

    /**
     * @param int                  $id
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/owner/bookings/{id}/cancel',
        operationId: 'ownerCancelBooking',
        description: 'Cancels a pending or confirmed booking at the owner`s barbershop',
        summary: 'Cancel booking',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Booking cancelled'),
            new OA\Response(response: 403, description: 'Not an owner'),
            new OA\Response(response: 404, description: 'Booking not found'),
            new OA\Response(response: 422, description: 'Invalid status'),
        ]
    )]
    public function cancelBooking(int $id, OwnerServiceContract $service): JsonResponse
    {
        return $service->cancelBooking($id);
    }

    /**
     * @param int                  $id
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/owner/bookings/{id}/complete',
        operationId: 'ownerCompleteBooking',
        description: 'Marks a confirmed booking as completed',
        summary: 'Complete booking',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Booking completed'),
            new OA\Response(response: 403, description: 'Not an owner'),
            new OA\Response(response: 404, description: 'Booking not found'),
            new OA\Response(response: 422, description: 'Invalid status'),
        ]
    )]
    public function completeBooking(int $id, OwnerServiceContract $service): JsonResponse
    {
        return $service->completeBooking($id);
    }

    /**
     * @param int                  $id
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/owner/bookings/{id}/confirm',
        operationId: 'ownerConfirmBooking',
        description: 'Confirms a pending booking',
        summary: 'Confirm booking',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Booking confirmed'),
            new OA\Response(response: 403, description: 'Not an owner'),
            new OA\Response(response: 404, description: 'Booking not found'),
            new OA\Response(response: 422, description: 'Only pending bookings can be confirmed'),
        ]
    )]
    public function confirmBooking(int $id, OwnerServiceContract $service): JsonResponse
    {
        return $service->confirmBooking($id);
    }

    /**
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/owner/services',
        operationId: 'ownerListServices',
        description: 'List all services of the owner`s barbershop',
        summary: 'List owner services',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        responses: [
            new OA\Response(response: 200, description: 'Services list'),
            new OA\Response(response: 403, description: 'Not an owner'),
        ]
    )]
    public function listServices(OwnerServiceContract $service): JsonResponse
    {
        return $service->listServices();
    }

    /**
     * @param ServiceStoreRequest  $request
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/owner/services',
        operationId: 'ownerCreateService',
        description: 'Create a new service for the owner`s barbershop',
        summary: 'Create service',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'category_name', 'price', 'duration_minutes'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Классическая стрижка'),
                    new OA\Property(property: 'category_name', type: 'string', example: 'Стрижки'),
                    new OA\Property(property: 'price', type: 'integer', example: 1500),
                    new OA\Property(property: 'duration_minutes', type: 'integer', example: 30),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Service created'),
            new OA\Response(response: 403, description: 'Not an owner'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function createService(ServiceStoreRequest $request, OwnerServiceContract $service): JsonResponse
    {
        return $service->createService($request);
    }

    /**
     * @param int                  $id
     * @param ServiceUpdateRequest $request
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Put(
        path: '/owner/services/{id}',
        operationId: 'ownerUpdateService',
        description: 'Update an existing service',
        summary: 'Update service',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'category_name', type: 'string'),
                    new OA\Property(property: 'price', type: 'integer'),
                    new OA\Property(property: 'duration_minutes', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Service updated'),
            new OA\Response(response: 403, description: 'Not an owner'),
            new OA\Response(response: 404, description: 'Service not found'),
        ]
    )]
    public function updateService(int $id, ServiceUpdateRequest $request, OwnerServiceContract $service): JsonResponse
    {
        return $service->updateService($id, $request);
    }

    /**
     * @param int                  $id
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Delete(
        path: '/owner/services/{id}',
        operationId: 'ownerDeleteService',
        description: 'Delete a service. Will fail if there are pending or confirmed bookings for it.',
        summary: 'Delete service',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Service deleted'),
            new OA\Response(response: 403, description: 'Not an owner'),
            new OA\Response(response: 404, description: 'Service not found'),
            new OA\Response(response: 422, description: 'Service has active bookings'),
        ]
    )]
    public function deleteService(int $id, OwnerServiceContract $service): JsonResponse
    {
        return $service->deleteService($id);
    }

    /**
     * @param AnalyticsRequest     $request
     * @param OwnerServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/owner/analytics',
        operationId: 'ownerAnalytics',
        description: 'Returns aggregated analytics for the owner`s barbershop with selectable period (week/month/year)',
        summary: 'Owner analytics',
        security: [['bearerAuth' => []]],
        tags: ['Owner'],
        parameters: [
            new OA\Parameter(name: 'period', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['week', 'month', 'year'], default: 'week')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Analytics data'),
            new OA\Response(response: 403, description: 'Not an owner'),
        ]
    )]
    public function analytics(AnalyticsRequest $request, OwnerServiceContract $service): JsonResponse
    {
        return $service->analytics($request);
    }
}
