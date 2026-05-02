<?php

namespace App\Contracts\Repositories;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryContract
{

    /**
     * @param array $data
     *
     * @return Review
     */
    public function create(array $data): Review;

    /**
     * @param int $userId
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getUserReviewsPaginated(int $userId, int $perPage = 15): LengthAwarePaginator;
}
