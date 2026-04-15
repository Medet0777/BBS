<?php

namespace App\Repositories;

use App\Contracts\Repositories\OwnerRepositoryContract;
use App\Enums\BookingStatus;
use App\Models\Barbershop;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OwnerRepository implements OwnerRepositoryContract
{

    /**
     * @param int $userId
     *
     * @return Barbershop|null
     */
    public function findBarbershopByOwner(int $userId): ?Barbershop
    {
        return Barbershop::where('owner_id', $userId)
            ->withAvg('reviews as avg_rating', 'rating')
            ->first();
    }

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return int
     */
    public function countBookingsInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): int
    {
        return Booking::where('barbershop_id', $barbershopId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->when(!empty($excludeStatuses), fn ($q) => $q->whereNotIn('status', $excludeStatuses))
            ->count();
    }

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return float
     */
    public function sumRevenueInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): float
    {
        return (float) Booking::where('barbershop_id', $barbershopId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->when(!empty($excludeStatuses), fn ($q) => $q->whereNotIn('status', $excludeStatuses))
            ->sum('total_price');
    }

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return int
     */
    public function countNewClientsInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): int
    {
        $rangeUserIds = Booking::where('barbershop_id', $barbershopId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->when(!empty($excludeStatuses), fn ($q) => $q->whereNotIn('status', $excludeStatuses))
            ->distinct()
            ->pluck('user_id');

        if ($rangeUserIds->isEmpty()) {
            return 0;
        }

        $returningUserIds = Booking::where('barbershop_id', $barbershopId)
            ->whereIn('user_id', $rangeUserIds)
            ->where('scheduled_at', '<', $from)
            ->distinct()
            ->pluck('user_id');

        return $rangeUserIds->diff($returningUserIds)->count();
    }

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return array
     */
    public function revenuePerDayInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): array
    {
        $rows = Booking::where('barbershop_id', $barbershopId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->when(!empty($excludeStatuses), fn ($q) => $q->whereNotIn('status', $excludeStatuses))
            ->select(DB::raw('DATE(scheduled_at) as day'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('day')
            ->pluck('revenue', 'day')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        return $rows;
    }

    /**
     * @param int $barbershopId
     * @param int $limit
     *
     * @return Collection
     */
    public function getNearestBookings(int $barbershopId, int $limit = 3): Collection
    {
        return Booking::where('barbershop_id', $barbershopId)
            ->where('scheduled_at', '>=', now())
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::Completed->value])
            ->with(['user', 'barber', 'services'])
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();
    }
}
