<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\BarbershopServiceContract;
use App\Http\Requests\Api\V1\Barbershop\ListRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class BarbershopController extends Controller
{

    /**
     * @param ListRequest               $request
     * @param BarbershopServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/barbershops',
        operationId: 'barbershopList',
        description: 'Returns paginated list of active barbershops. Supports search by name, filter by open status, sort by rating or distance (requires user coordinates).',
        summary: 'List all barbershops',
        tags: ['Barbershop'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Search by barbershop name', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'is_open', in: 'query', description: 'Filter barbershops currently open', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'order_by', in: 'query', description: 'Sort order', required: false, schema: new OA\Schema(type: 'string', enum: ['rating', 'distance'])),
            new OA\Parameter(name: 'user_lat', in: 'query', description: 'User latitude (required for distance calculation)', required: false, schema: new OA\Schema(type: 'number', format: 'float', example: 43.238949)),
            new OA\Parameter(name: 'user_lng', in: 'query', description: 'User longitude (required for distance calculation)', required: false, schema: new OA\Schema(type: 'number', format: 'float', example: 76.889709)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of barbershops',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'BarbershopKZ'),
                            new OA\Property(property: 'slug', type: 'string', example: 'barbershop-kz'),
                            new OA\Property(property: 'logo', type: 'string', nullable: true),
                            new OA\Property(property: 'address', type: 'string', example: 'ул. Абая 14'),
                            new OA\Property(property: 'rating', type: 'number', format: 'float', example: 4.9),
                            new OA\Property(property: 'reviews_count', type: 'integer', example: 312),
                            new OA\Property(property: 'opens_at', type: 'string', example: '09:00'),
                            new OA\Property(property: 'closes_at', type: 'string', example: '21:00'),
                            new OA\Property(property: 'status', type: 'string', enum: ['open', 'closed'], example: 'open'),
                            new OA\Property(property: 'distance_km', type: 'number', format: 'float', nullable: true, example: 0.4),
                        ])),
                        new OA\Property(property: 'current_page', type: 'integer', example: 1),
                        new OA\Property(property: 'last_page', type: 'integer', example: 3),
                        new OA\Property(property: 'per_page', type: 'integer', example: 10),
                        new OA\Property(property: 'total', type: 'integer', example: 24),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'message', type: 'string'),
                    new OA\Property(property: 'errors', type: 'object'),
                ])
            ),
        ]
    )]
    public function list(ListRequest $request, BarbershopServiceContract $service): JsonResponse
    {
        return $service->list($request);
    }

    /**
     * @param string                    $slug
     * @param BarbershopServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/barbershops/{slug}',
        operationId: 'barbershopShow',
        description: 'Returns barbershop details with services grouped by category',
        summary: 'Show barbershop by slug',
        tags: ['Barbershop'],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string', example: 'barbershop-kz')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Barbershop details',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'BarbershopKZ'),
                        new OA\Property(property: 'slug', type: 'string', example: 'barbershop-kz'),
                        new OA\Property(property: 'description', type: 'string', nullable: true),
                        new OA\Property(property: 'logo', type: 'string', nullable: true),
                        new OA\Property(property: 'phone', type: 'string', example: '+77001234567'),
                        new OA\Property(property: 'address', type: 'string', example: 'ул. Абая 14'),
                        new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 43.238949),
                        new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 76.889709),
                        new OA\Property(property: 'rating', type: 'number', format: 'float', example: 4.9),
                        new OA\Property(property: 'reviews_count', type: 'integer', example: 312),
                        new OA\Property(property: 'opens_at', type: 'string', example: '09:00'),
                        new OA\Property(property: 'closes_at', type: 'string', example: '21:00'),
                        new OA\Property(property: 'status', type: 'string', enum: ['open', 'closed'], example: 'open'),
                        new OA\Property(property: 'reviews', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'user_name', type: 'string'),
                            new OA\Property(property: 'rating', type: 'integer'),
                            new OA\Property(property: 'comment', type: 'string', nullable: true),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        ])),
                        new OA\Property(property: 'barbers', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Alikhan Satybaldy'),
                            new OA\Property(property: 'avatar', type: 'string', nullable: true),
                            new OA\Property(property: 'specialization', type: 'string', nullable: true, example: 'Классические стрижки'),
                            new OA\Property(property: 'rating', type: 'number', format: 'float', example: 4.8),
                            new OA\Property(property: 'experience_years', type: 'integer', example: 5),
                        ])),
                        new OA\Property(property: 'services', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'category_id', type: 'integer', example: 1),
                            new OA\Property(property: 'category_name', type: 'string', example: 'Стрижки'),
                            new OA\Property(property: 'items', type: 'array', items: new OA\Items(properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Классическая стрижка'),
                                new OA\Property(property: 'price', type: 'number', format: 'float', example: 1500),
                                new OA\Property(property: 'duration_minutes', type: 'integer', example: 30),
                            ])),
                        ])),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(
                response: 404,
                description: 'Barbershop not found',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Barbershop not found'),
                    new OA\Property(property: 'error', type: 'string', example: 'not_found'),
                ])
            ),
        ]
    )]
    public function show(string $slug, BarbershopServiceContract $service): JsonResponse
    {
        return $service->show($slug);
    }
}
