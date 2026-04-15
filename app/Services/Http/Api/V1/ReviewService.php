<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Contracts\Repositories\ReviewRepositoryContract;
use App\Contracts\Services\Http\Api\V1\ReviewServiceContract;
use App\Http\Requests\Api\V1\Review\CreateRequest;
use App\Http\Resources\Api\V1\Review\ReviewResource;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReviewService implements ReviewServiceContract
{

    use ApiResponse;

    /**
     * @param ReviewRepositoryContract     $reviewRepository
     * @param BarbershopRepositoryContract $barbershopRepository
     */
    public function __construct(
        private readonly ReviewRepositoryContract     $reviewRepository,
        private readonly BarbershopRepositoryContract $barbershopRepository,
    ) {
    }

    /**
     * @param string        $slug
     * @param CreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(string $slug, CreateRequest $request): JsonResponse
    {
        $barbershop = $this->barbershopRepository->findBySlug($slug);

        if (!$barbershop) {
            return $this->error('Barbershop not found', 'not_found', 404);
        }

        $review = $this->reviewRepository->create([
            'user_id'       => auth()->id(),
            'barbershop_id' => $barbershop->id,
            'rating'        => $request->integer('rating'),
            'comment'       => $request->input('comment'),
        ]);

        return $this->success(new ReviewResource($review), 'Review created', 201);
    }
}
