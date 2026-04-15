<?php

namespace App\Contracts\Repositories;

use App\Models\Booking;
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
}
