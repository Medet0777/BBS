<?php

namespace App\Repositories;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Enums\BookingStatus;
use App\Models\Barbershop;
use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
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
            ->withCount('reviews')
            ->withMin('services as min_price', 'price');

        if ($userLat !== null && $userLng !== null) {
            $lat = (float) $userLat;
            $lng = (float) $userLng;
            $haversine = "(6371 * acos(cos(radians({$lat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$lng})) + sin(radians({$lat})) * sin(radians(latitude))))";
            $query->addSelect('barbershops.*')->selectRaw("{$haversine} AS distance_km");
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
            ->withMin('services as min_price', 'price')
            ->with([
                'services.serviceCategory',
                'barbers' => fn ($query) => $query->where('is_active', true),
                'reviews' => fn ($query) => $query->with('user')->latest()->limit(20),
            ])
            ->first();
    }

    /**
     * @param int      $barbershopId
     * @param string   $date
     * @param int|null $barberId
     *
     * @return Collection
     */
    public function getBookingsForDate(int $barbershopId, string $date, ?int $barberId = null): Collection
    {
        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd   = Carbon::parse($date)->endOfDay();

        return Booking::where('barbershop_id', $barbershopId)
            ->when($barberId, fn ($q) => $q->where('barber_id', $barberId))
            ->whereBetween('scheduled_at', [$dayStart, $dayEnd])
            ->whereNotIn('status', [BookingStatus::Cancelled->value])
            ->get(['scheduled_at', 'total_duration_minutes']);
    }
}
