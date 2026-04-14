<?php

namespace App\Contracts\Repositories;

use App\Models\Barbershop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BarbershopRepositoryContract
{

    /**
     * @param int         $perPage
     * @param string|null $orderBy
     * @param float|null  $userLat
     * @param float|null  $userLng
     * @param bool        $onlyOpen
     * @param string|null $search
     *
     * @return LengthAwarePaginator
     */
    public function getAll(
        int $perPage = 10,
        ?string $orderBy = null,
        ?float $userLat = null,
        ?float $userLng = null,
        bool $onlyOpen = false,
        ?string $search = null,
    ): LengthAwarePaginator;

    /**
     * @param string $slug
     *
     * @return Barbershop|null
     */
    public function findBySlug(string $slug): ?Barbershop;
}
