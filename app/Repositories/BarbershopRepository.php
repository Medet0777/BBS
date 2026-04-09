<?php

namespace App\Repositories;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Models\Barbershop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BarbershopRepository implements BarbershopRepositoryContract
{

    /**
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Barbershop::where('is_active', true)->paginate($perPage);
    }

    /**
     * @param string $slug
     *
     * @return Barbershop|null
     */
    public function findBySlug(string $slug): ?Barbershop
    {
        return Barbershop::where('slug', $slug)
            ->where('is_active', true)
            ->with('services.serviceCategory')
            ->first();
    }
}
