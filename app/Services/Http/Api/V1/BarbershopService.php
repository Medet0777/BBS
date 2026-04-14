<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Contracts\Services\Http\Api\V1\BarbershopServiceContract;
use App\Http\Requests\Api\V1\Barbershop\ListRequest;
use App\Http\Resources\Api\V1\Barbershop\ListResource;
use App\Http\Resources\Api\V1\Barbershop\ShowResource;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;

class BarbershopService implements BarbershopServiceContract
{

    use ApiResponse;

    /**
     * @param BarbershopRepositoryContract $barbershopRepository
     */
    public function __construct(
        private readonly BarbershopRepositoryContract $barbershopRepository,
    ) {
    }

    /**
     * @param ListRequest $request
     *
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse
    {
        $perPage  = $request->integer('per_page', 10);
        $orderBy  = $request->input('order_by');
        $userLat  = $request->filled('user_lat') ? (float) $request->input('user_lat') : null;
        $userLng  = $request->filled('user_lng') ? (float) $request->input('user_lng') : null;
        $onlyOpen = $request->boolean('is_open');
        $search   = $request->input('search');

        $barbershops = $this->barbershopRepository->getAll($perPage, $orderBy, $userLat, $userLng, $onlyOpen, $search);

        return $this->success(ListResource::collection($barbershops)->response()->getData(true));
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $barbershop = $this->barbershopRepository->findBySlug($slug);

        if (!$barbershop) {
            return $this->error('Barbershop not found', 'not_found', 404);
        }

        return $this->success(new ShowResource($barbershop));
    }
}
