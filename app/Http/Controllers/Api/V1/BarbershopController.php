<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\BarbershopServiceContract;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class BarbershopController extends Controller
{

    /**
     * @param BarbershopServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/barbershops',
        operationId: 'barbershopList',
        description: 'Returns list of all active barbershops',
        summary: 'List all barbershops',
        tags: ['Barbershop'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of barbershops',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'BarbershopKZ'),
                        new OA\Property(property: 'slug', type: 'string', example: 'barbershop-kz'),
                        new OA\Property(property: 'logo', type: 'string', nullable: true),
                        new OA\Property(property: 'address', type: 'string', example: 'ул. Абая 14'),
                        new OA\Property(property: 'rating', type: 'number', example: 4.9),
                        new OA\Property(property: 'opens_at', type: 'string', example: '09:00'),
                        new OA\Property(property: 'closes_at', type: 'string', example: '21:00'),
                    ])),
                ])
            ),
        ]
    )]
    public function list(BarbershopServiceContract $service): JsonResponse
    {
        return $service->list();
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
        description: 'Returns barbershop details with services',
        summary: 'Show barbershop by slug',
        tags: ['Barbershop'],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Barbershop details',
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
