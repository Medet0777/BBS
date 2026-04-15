<?php

namespace App\Repositories;

use App\Contracts\Repositories\BookingRepositoryContract;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

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
}
