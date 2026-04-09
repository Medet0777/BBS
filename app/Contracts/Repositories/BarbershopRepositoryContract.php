<?php

namespace App\Contracts\Repositories;

use App\Models\Barbershop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BarbershopRepositoryContract
{

    /**
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator;

    /**
     * @param string $slug
     *
     * @return Barbershop|null
     */
    public function findBySlug(string $slug): ?Barbershop;
}
