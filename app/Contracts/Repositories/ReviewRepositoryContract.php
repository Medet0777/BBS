<?php

namespace App\Contracts\Repositories;

use App\Models\Review;

interface ReviewRepositoryContract
{

    /**
     * @param array $data
     *
     * @return Review
     */
    public function create(array $data): Review;
}
