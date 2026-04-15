<?php

namespace App\Contracts\Repositories;

use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BookingRepositoryContract
{

    /**
     * @param array $data
     * @param array $servicesPivot
     *
     * @return Booking
     */
    public function create(array $data, array $servicesPivot): Booking;

    /**
     * @param array $serviceIds
     *
     * @return Collection
     */
    public function getServicesByIds(array $serviceIds): Collection;

    /**
     * @param int         $userId
     * @param string|null $filter
     * @param int         $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getUserBookings(int $userId, ?string $filter = null, int $perPage = 10): LengthAwarePaginator;

    /**
     * @param int $id
     * @param int $userId
     *
     * @return Booking|null
     */
    public function findForUser(int $id, int $userId): ?Booking;

    /**
     * @param Booking $booking
     * @param array   $data
     *
     * @return Booking
     */
    public function update(Booking $booking, array $data): Booking;
}
