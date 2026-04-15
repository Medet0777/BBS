<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\BookingRepositoryContract;
use App\Contracts\Services\Http\Api\V1\BookingServiceContract;
use App\Enums\BookingStatus;
use App\Http\Requests\Api\V1\Booking\CreateRequest;
use App\Http\Requests\Api\V1\Booking\ListRequest;
use App\Http\Resources\Api\V1\Booking\CreateResource;
use App\Http\Resources\Api\V1\Booking\ListResource;
use App\Models\Barber;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;

class BookingService implements BookingServiceContract
{

    use ApiResponse;

    /**
     * @param BookingRepositoryContract $bookingRepository
     */
    public function __construct(
        private readonly BookingRepositoryContract $bookingRepository,
    ) {
    }

    /**
     * @param CreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $barbershopId = $request->integer('barbershop_id');
        $barberId     = $request->integer('barber_id');
        $serviceIds   = $request->input('service_ids');

        // 1. Check barber belongs to chosen barbershop
        $barber = Barber::where('id', $barberId)
            ->where('barbershop_id', $barbershopId)
            ->where('is_active', true)
            ->first();

        if (!$barber) {
            return $this->error('Barber does not belong to this barbershop', 'invalid_barber', 422);
        }

        // 2. Load services and verify they all belong to this barbershop
        $services = $this->bookingRepository->getServicesByIds($serviceIds);

        if ($services->count() !== count($serviceIds)) {
            return $this->error('Some services were not found', 'invalid_services', 422);
        }

        if ($services->contains(fn ($s) => $s->barbershop_id !== $barbershopId)) {
            return $this->error('Services do not belong to this barbershop', 'invalid_services', 422);
        }

        // 3. Calculate totals + build pivot payload with price/duration snapshot
        $totalPrice    = 0;
        $totalDuration = 0;
        $pivot         = [];

        foreach ($services as $service) {
            $totalPrice    += (float) $service->price;
            $totalDuration += (int) $service->duration_minutes;
            $pivot[$service->id] = [
                'price_snapshot'    => $service->price,
                'duration_snapshot' => $service->duration_minutes,
            ];
        }

        // 4. Persist booking + attach services
        $booking = $this->bookingRepository->create([
            'user_id'                => auth()->id(),
            'barbershop_id'          => $barbershopId,
            'barber_id'              => $barberId,
            'scheduled_at'           => $request->input('scheduled_at'),
            'status'                 => BookingStatus::Pending->value,
            'comment'                => $request->input('comment'),
            'reminder_enabled'       => $request->boolean('reminder_enabled'),
            'total_price'            => $totalPrice,
            'total_duration_minutes' => $totalDuration,
        ], $pivot);

        return $this->success(new CreateResource($booking), 'Booking created', 201);
    }

    /**
     * @param ListRequest $request
     *
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse
    {
        $filter  = $request->input('filter');
        $perPage = $request->integer('per_page', 10);

        $bookings = $this->bookingRepository->getUserBookings(auth()->id(), $filter, $perPage);

        return $this->success(ListResource::collection($bookings)->response()->getData(true));
    }
}
