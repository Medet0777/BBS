<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\OwnerServiceContract;
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
}
