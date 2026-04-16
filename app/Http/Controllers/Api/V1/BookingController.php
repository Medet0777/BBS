<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\BookingServiceContract;
use App\Http\Requests\Api\V1\Booking\CreateRequest;
use App\Http\Requests\Api\V1\Booking\ListRequest;
use App\Http\Requests\Api\V1\Booking\RescheduleRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class BookingController extends Controller
{

    /**
     * @param CreateRequest          $request
     * @param BookingServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/bookings',
        operationId: 'bookingCreate',
        description: 'Creates a new booking for the authenticated user. Calculates total price/duration from selected services.',
        summary: 'Create a booking',
        security: [['bearerAuth' => []]],
        tags: ['Booking'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['barbershop_id', 'barber_id', 'scheduled_at', 'service_ids'],
                properties: [
                    new OA\Property(property: 'barbershop_id', type: 'integer', example: 1),
                    new OA\Property(property: 'barber_id', type: 'integer', example: 1),
                    new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time', example: '2026-04-24T11:00:00'),
                    new OA\Property(property: 'service_ids', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2]),
                    new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Сделайте короче'),
                    new OA\Property(property: 'reminder_enabled', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Booking created',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Booking created'),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'barbershop_name', type: 'string', example: 'BarbershopKZ'),
                        new OA\Property(property: 'barbershop_address', type: 'string', example: 'ул. Абая 14'),
                        new OA\Property(property: 'barber_name', type: 'string', example: 'Alikhan Satybaldy'),
                        new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'confirmed', 'cancelled', 'completed']),
                        new OA\Property(property: 'services', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'price', type: 'number', format: 'float'),
                            new OA\Property(property: 'duration_minutes', type: 'integer'),
                        ])),
                        new OA\Property(property: 'total_price', type: 'number', format: 'float', example: 1500),
                        new OA\Property(property: 'total_duration_minutes', type: 'integer', example: 30),
                        new OA\Property(property: 'comment', type: 'string', nullable: true),
                        new OA\Property(property: 'reminder_enabled', type: 'boolean'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(CreateRequest $request, BookingServiceContract $service): JsonResponse
    {
        return $service->create($request);
    }

    /**
     * @param int                    $id
     * @param BookingServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/bookings/{id}',
        operationId: 'bookingShow',
        description: 'Returns booking details with services, barbershop and barber info',
        summary: 'Show booking detail',
        security: [['bearerAuth' => []]],
        tags: ['Booking'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Booking details',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'barbershop_name', type: 'string'),
                        new OA\Property(property: 'barbershop_address', type: 'string'),
                        new OA\Property(property: 'barber_name', type: 'string'),
                        new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'services', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'price', type: 'number'),
                            new OA\Property(property: 'duration_minutes', type: 'integer'),
                        ])),
                        new OA\Property(property: 'total_price', type: 'number'),
                        new OA\Property(property: 'total_duration_minutes', type: 'integer'),
                        new OA\Property(property: 'comment', type: 'string', nullable: true),
                        new OA\Property(property: 'reminder_enabled', type: 'boolean'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Booking not found'),
        ]
    )]
    public function show(int $id, BookingServiceContract $service): JsonResponse
    {
        return $service->show($id);
    }

    /**
     * @param ListRequest            $request
     * @param BookingServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/bookings',
        operationId: 'bookingList',
        description: 'Returns paginated list of authenticated user bookings. Filter by upcoming or past.',
        summary: 'My bookings',
        security: [['bearerAuth' => []]],
        tags: ['Booking'],
        parameters: [
            new OA\Parameter(name: 'filter', in: 'query', description: 'upcoming or past', required: false, schema: new OA\Schema(type: 'string', enum: ['upcoming', 'past'])),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated bookings',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'barbershop_name', type: 'string', example: 'BarbershopKZ'),
                            new OA\Property(property: 'barbershop_logo', type: 'string', nullable: true),
                            new OA\Property(property: 'barber_name', type: 'string', example: 'Alikhan'),
                            new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'total_price', type: 'number', format: 'float', example: 1500),
                            new OA\Property(property: 'status', type: 'string', enum: ['pending', 'confirmed', 'cancelled', 'completed']),
                        ])),
                        new OA\Property(property: 'current_page', type: 'integer'),
                        new OA\Property(property: 'last_page', type: 'integer'),
                        new OA\Property(property: 'per_page', type: 'integer'),
                        new OA\Property(property: 'total', type: 'integer'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function list(ListRequest $request, BookingServiceContract $service): JsonResponse
    {
        return $service->list($request);
    }

    /**
     * @param int                    $id
     * @param BookingServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/bookings/{id}/cancel',
        operationId: 'bookingCancel',
        description: 'Cancels a pending or confirmed booking',
        summary: 'Cancel booking',
        security: [['bearerAuth' => []]],
        tags: ['Booking'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Booking cancelled'),
            new OA\Response(response: 404, description: 'Booking not found'),
            new OA\Response(response: 422, description: 'Invalid status'),
        ]
    )]
    public function cancel(int $id, BookingServiceContract $service): JsonResponse
    {
        return $service->cancel($id);
    }

    /**
     * @param int                    $id
     * @param RescheduleRequest      $request
     * @param BookingServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/bookings/{id}/reschedule',
        operationId: 'bookingReschedule',
        description: 'Reschedules a pending or confirmed booking to a new datetime',
        summary: 'Reschedule booking',
        security: [['bearerAuth' => []]],
        tags: ['Booking'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['scheduled_at'],
                properties: [
                    new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time', example: '2026-04-25T15:00:00'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Booking rescheduled'),
            new OA\Response(response: 404, description: 'Booking not found'),
            new OA\Response(response: 422, description: 'Invalid status or date'),
        ]
    )]
    public function reschedule(int $id, RescheduleRequest $request, BookingServiceContract $service): JsonResponse
    {
        return $service->reschedule($id, $request);
    }
}
