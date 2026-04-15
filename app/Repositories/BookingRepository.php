<?php

namespace App\Repositories;

use App\Contracts\Repositories\BookingRepositoryContract;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class BookingRepository implements BookingRepositoryContract
{

    /**
     * @param array $data
     * @param array $servicesPivot
     *
     * @return Booking
     */
    public function create(array $data, array $servicesPivot): Booking
    {
        $booking = Booking::create($data);
        $booking->services()->attach($servicesPivot);
        $booking->load(['barbershop', 'barber', 'services']);

        return $booking;
    }

    /**
     * @param array $serviceIds
     *
     * @return Collection
     */
    public function getServicesByIds(array $serviceIds): Collection
    {
        return Service::whereIn('id', $serviceIds)->get();
    }

    /**
     * @param int         $userId
     * @param string|null $filter
     * @param int         $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getUserBookings(int $userId, ?string $filter = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Booking::where('user_id', $userId)
            ->with(['barbershop', 'barber']);

        $now = Carbon::now();

        if ($filter === 'upcoming') {
            $query->where('scheduled_at', '>=', $now)
                  ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::Completed->value])
                  ->orderBy('scheduled_at');
        } elseif ($filter === 'past') {
            $query->where(function ($q) use ($now) {
                $q->where('scheduled_at', '<', $now)
                  ->orWhereIn('status', [BookingStatus::Cancelled->value, BookingStatus::Completed->value]);
            })->orderByDesc('scheduled_at');
        } else {
            $query->orderByDesc('scheduled_at');
        }

        return $query->paginate($perPage);
    }

    /**
     * @param int $id
     * @param int $userId
     *
     * @return Booking|null
     */
    public function findForUser(int $id, int $userId): ?Booking
    {
        return Booking::where('id', $id)
            ->where('user_id', $userId)
            ->with(['barbershop', 'barber', 'services'])
            ->first();
    }

    /**
     * @param Booking $booking
     * @param array   $data
     *
     * @return Booking
     */
    public function update(Booking $booking, array $data): Booking
    {
        $booking->update($data);

        return $booking->fresh(['barbershop', 'barber', 'services']);
    }
}
