<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReviewRepositoryContract;
use App\Models\Review;

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
}
