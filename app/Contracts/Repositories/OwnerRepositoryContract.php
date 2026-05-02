<?php

namespace App\Contracts\Repositories;

use App\Models\Barbershop;

interface OwnerRepositoryContract
{

    /**
     * @param int $userId
     *
     * @return Barbershop|null
     */
    public function findBarbershopByOwner(int $userId): ?Barbershop;

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return int
     */
    public function countBookingsInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): int;

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return float
     */
    public function sumRevenueInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): float;

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return int
     */
    public function countNewClientsInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): int;

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return array
     */
    public function revenuePerDayInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): array;

    /**
     * @param int $barbershopId
     * @param int $limit
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNearestBookings(int $barbershopId, int $limit = 3): \Illuminate\Database\Eloquent\Collection;

    /**
     * @param int         $barbershopId
     * @param string|null $filter
     * @param int         $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getBookingsPaginated(int $barbershopId, ?string $filter, int $perPage): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * @param int $bookingId
     * @param int $barbershopId
     *
     * @return \App\Models\Booking|null
     */
    public function findBookingByBarbershop(int $bookingId, int $barbershopId): ?\App\Models\Booking;

    /**
     * @param \App\Models\Booking $booking
     * @param array               $data
     *
     * @return \App\Models\Booking
     */
    public function updateBooking(\App\Models\Booking $booking, array $data): \App\Models\Booking;

    /**
     * @param int $barbershopId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getServices(int $barbershopId): \Illuminate\Database\Eloquent\Collection;

    /**
     * @param int $serviceId
     * @param int $barbershopId
     *
     * @return \App\Models\Service|null
     */
    public function findServiceByBarbershop(int $serviceId, int $barbershopId): ?\App\Models\Service;

    /**
     * @param array $data
     *
     * @return \App\Models\Service
     */
    public function createService(array $data): \App\Models\Service;

    /**
     * @param \App\Models\Service $service
     * @param array               $data
     *
     * @return \App\Models\Service
     */
    public function updateService(\App\Models\Service $service, array $data): \App\Models\Service;

    /**
     * @param \App\Models\Service $service
     *
     * @return void
     */
    public function deleteService(\App\Models\Service $service): void;

    /**
     * @param int $serviceId
     *
     * @return bool
     */
    public function serviceHasActiveBookings(int $serviceId): bool;

    /**
     * @param string $name
     *
     * @return \App\Models\ServiceCategory
     */
    public function firstOrCreateCategory(string $name): \App\Models\ServiceCategory;

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param array  $excludeStatuses
     *
     * @return array
     */
    public function bookingsPerDayInRange(int $barbershopId, string $from, string $to, array $excludeStatuses = []): array;

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     * @param int    $limit
     * @param array  $excludeStatuses
     *
     * @return array
     */
    public function topServicesInRange(int $barbershopId, string $from, string $to, int $limit = 5, array $excludeStatuses = []): array;

    /**
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookingsInRange(int $barbershopId, string $from, string $to): \Illuminate\Database\Eloquent\Collection;
}
