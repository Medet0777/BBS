<?php

namespace App\Repositories;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Models\Barbershop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class BarbershopRepository implements BarbershopRepositoryContract
{

    /**
     * @param int         $perPage
     * @param string|null $orderBy
     * @param float|null  $userLat
     * @param float|null  $userLng
     * @param bool        $onlyOpen
     * @param string|null $search
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $perPage = 10,
        ?string $orderBy = null,
        ?float $userLat = null,
        ?float $userLng = null,
        bool $onlyOpen = false,
        ?string $search = null,
    ): LengthAwarePaginator {
        $query = Barbershop::where('is_active', true)
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews');

        if ($userLat !== null && $userLng !== null) {
            $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';
            $query->selectRaw("*, {$haversine} AS distance_km", [$userLat, $userLng, $userLat]);
        }

        if ($search !== null) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($onlyOpen) {
            $currentTime = Carbon::now()->format('H:i');
            $query->where('opens_at', '<=', $currentTime)
                  ->where('closes_at', '>=', $currentTime);
        }

        if ($orderBy === 'rating') {
            $query->orderByDesc('rating');
        }

        if ($orderBy === 'distance' && $userLat !== null && $userLng !== null) {
            $query->orderBy('distance_km');
        }

        return $query->paginate($perPage);
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
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews')
            ->with([
                'services.serviceCategory',
                'barbers' => fn ($query) => $query->where('is_active', true),
                'reviews' => fn ($query) => $query->with('user')->latest()->limit(20),
            ])
            ->first();
    }
}
