<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReviewRepositoryContract;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryContract
{

    /**
     * @param array $data
     *
     * @return Review
     */
    public function create(array $data): Review
    {
        return Review::create($data);
    }

    /**
     * @param int $userId
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getUserReviewsPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Review::where('user_id', $userId)
            ->with(['barbershop:id,name,slug,logo'])
            ->latest()
            ->paginate($perPage);
    }
}
