<?php

namespace App\Repositories;

use App\Contracts\Repositories\OwnerRepositoryContract;
use App\Enums\BookingStatus;
use App\Models\Barbershop;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     *
     * @return Collection
     */
    public function getBookingsInRange(int $barbershopId, string $from, string $to): Collection
    {
        return Booking::where('barbershop_id', $barbershopId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->with(['user', 'barber'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * @param int         $barbershopId
     * @param string|null $filter
     * @param int         $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getBookingsPaginated(int $barbershopId, ?string $filter, int $perPage): LengthAwarePaginator
    {
        $query = Booking::where('barbershop_id', $barbershopId)
            ->with(['user', 'barber', 'services']);

        if ($filter && $filter !== 'all') {
            $query->where('status', $filter);
        }

        return $query->orderByDesc('scheduled_at')->paginate($perPage);
    }

    /**
     * @param int $bookingId
     * @param int $barbershopId
     *
     * @return Booking|null
     */
    public function findBookingByBarbershop(int $bookingId, int $barbershopId): ?Booking
    {
        return Booking::where('id', $bookingId)
            ->where('barbershop_id', $barbershopId)
            ->first();
    }

    /**
     * @param Booking $booking
     * @param array   $data
     *
     * @return Booking
     */
    public function updateBooking(Booking $booking, array $data): Booking
    {
        $booking->update($data);

        return $booking->fresh(['user', 'barber', 'services']);
    }

    /**
     * @param int $barbershopId
     *
     * @return Collection
     */
    public function getServices(int $barbershopId): Collection
    {
        return Service::where('barbershop_id', $barbershopId)
            ->with('serviceCategory')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param int $serviceId
     * @param int $barbershopId
     *
     * @return Service|null
     */
    public function findServiceByBarbershop(int $serviceId, int $barbershopId): ?Service
    {
        return Service::where('id', $serviceId)
            ->where('barbershop_id', $barbershopId)
            ->with('serviceCategory')
            ->first();
    }

    /**
     * @param array $data
     *
     * @return Service
     */
    public function createService(array $data): Service
    {
        $service = Service::create($data);
        $service->load('serviceCategory');

        return $service;
    }

    /**
     * @param Service $service
     * @param array   $data
     *
     * @return Service
     */
    public function updateService(Service $service, array $data): Service
    {
        $service->update($data);

        return $service->fresh(['serviceCategory']);
    }

    /**
     * @param Service $service
     *
     * @return void
     */
    public function deleteService(Service $service): void
    {
        $service->delete();
    }

    /**
     * @param int $serviceId
     *
     * @return bool
     */
    public function serviceHasActiveBookings(int $serviceId): bool
    {
        return DB::table('booking_service')
            ->join('bookings', 'bookings.id', '=', 'booking_service.booking_id')
            ->where('booking_service.service_id', $serviceId)
            ->whereIn('bookings.status', [BookingStatus::Pending->value, BookingStatus::Confirmed->value])
            ->exists();
    }

    /**
     * @param string $name
     *
     * @return ServiceCategory
     */
    public function firstOrCreateCategory(string $name): ServiceCategory
    {
        return ServiceCategory::firstOrCreate(
            ['name' => $name],
            ['sort_order' => 0],
        );
    }
}
