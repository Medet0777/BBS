<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\ReviewServiceContract;
use App\Http\Requests\Api\V1\Review\CreateRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{

    /**
     * @param string                $slug
     * @param CreateRequest         $request
     * @param ReviewServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/barbershops/{slug}/reviews',
        operationId: 'reviewCreate',
        description: 'Creates a new review for the barbershop',
        summary: 'Leave a review',
        security: [['bearerAuth' => []]],
        tags: ['Review'],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['rating'],
                properties: [
                    new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
                    new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Очень понравилось!'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Review created',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Review created'),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'user_name', type: 'string'),
                        new OA\Property(property: 'rating', type: 'integer'),
                        new OA\Property(property: 'comment', type: 'string', nullable: true),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Barbershop not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(string $slug, CreateRequest $request, ReviewServiceContract $service): JsonResponse
    {
        return $service->create($slug, $request);
    }
}
