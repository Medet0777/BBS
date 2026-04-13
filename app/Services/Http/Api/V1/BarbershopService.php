<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Contracts\Services\Http\Api\V1\BarbershopServiceContract;
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
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $barbershops = $this->barbershopRepository->getAll();

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
