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
     * @param int    $barbershopId
     * @param string $from
     * @param string $to
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookingsInRange(int $barbershopId, string $from, string $to): \Illuminate\Database\Eloquent\Collection;
}
